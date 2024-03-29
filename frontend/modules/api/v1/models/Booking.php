<?php
namespace frontend\modules\api\v1\models;

use common\components\Mandrill;
use common\models\Doctor;
use common\models\InquiryDoctorList;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\BaseInflector;
use yii\web\BadRequestHttpException;

class Booking extends Model
{
    public $inquiry_doctor_id;
    public $first_name;
    public $last_name;
    public $phone_number;
    public $date;
    public $email;

    public $inquiry;

    public function rules()
    {
        return [
            [['inquiry_doctor_id', 'first_name', 'last_name', 'email', 'phone_number', 'date'], 'required'],
            ['email', 'email'],
            ['date', 'date', 'format' => 'MM/dd/yyyy HH:mm'],
            ['date', 'checkExistingDate'],
            [['inquiry_doctor_id'], 'checkInquiry'],
            [['first_name', 'last_name', 'phone_number'], 'string'],
        ];
    }

    /**
     * Validation for inquiry id.
     */
    public function checkInquiry()
    {
        $models = InquiryDoctorList::findAll($this->inquiry_doctor_id);
        if (!empty($models)) {
            $this->inquiry = $models[0];
            foreach ($models as $model) {
                /* @var $model InquiryDoctorList */
                if ($model->status == $model::STATUS_FINALIZED) {
                    $this->addError('inquiry_doctor_id', Yii::t('app', 'You can`t book paid offer.'));
                } elseif ($model->status == $model::STATUS_BOOKED) {
                    $this->addError('inquiry_doctor_id', Yii::t('app', 'Already booked.'));
                }
                
                if (!Yii::$app->user->isGuest) {
                    if ($model->inquiry->user_id != Yii::$app->user->id) {
                        $this->addError('inquiry_doctor_id', Yii::t('app', 'This is not your inquiry doctor.'));
                    }
                } else {
                    if ($model->inquiry->user_id != User::GUEST_ACCOUNT_ID) {
                        $this->addError('inquiry_doctor_id', Yii::t('app', 'This is not your inquiry doctor.'));
                    }
                }
            }
        } else {
            $this->addError('inquiry_doctor_id', Yii::t('app', 'Inquiry doctor not found.'));
        }
    }

    public function checkExistingDate($attribute, $params)
    {
        $start_date = date("Y-m-d H:i:s", strtotime($this->$attribute) - 60 * 60);
        $end_date = date("Y-m-d H:i:s", strtotime($this->$attribute) + 60 * 60);
        $bookings = \common\models\Booking::find()
            ->where(['inquiry_id' => $this->inquiry])
            ->andWhere(['between', 'date', $start_date, $end_date])
            ->all();
        if (!empty($bookings)) {
            $this->addError($attribute, Yii::t('app', '{date} is already booked, please pick another one',
                ['date' => date("Y-m-d H:i", strtotime($this->$attribute)) ]));
        }
    }

    public function book()
    {
        $booked_offers = InquiryDoctorList::findAll($this->inquiry_doctor_id);
        if (!empty($booked_offers)) {
            foreach ($booked_offers as $offer) {
                $offer->status = $offer::STATUS_BOOKED;
                $offer->update(false);
            }

            $booking = new \common\models\Booking();
            $booking->date = $this->date;
            $booking->phone_number = $this->phone_number;
            $booking->email = $this->email;
            $booking->first_name = $this->first_name;
            $booking->last_name = $this->last_name;
            $booking->inquiry_id = $booked_offers[0]->inquiry_id;
            $booking->is_viewed = 0;
            $booking->is_viewed_admin = 0;
            
            if ($booking->save(false) && $this->sendMail($booked_offers, $booking->date)) {
                return true;
            }

            return false;
        } else {
            throw new BadRequestHttpException;
        }
    }

    public function sendMail($booked_offers, $date)
    {
        /** @var InquiryDoctorList $list_model */
        foreach ($booked_offers as $list_model) {
            /** @var Doctor $doctor */
            $doctor = Doctor::findOne(['user_id' => $list_model->user_id]);
            $offer = $list_model->inquiry->getOfferData($list_model->inquiry_id, $list_model->user_id);

            foreach ($offer[$list_model->user_id]['data'] as $offer_data) {
                if (isset($offer_data['brand'])) {
                    $item = Yii::t('app', 'Item: ') . $offer_data['brand'] . ', ';
                    if ((is_numeric((int)$offer_data['param_value']))) {
                        $item .= (int)$offer_data['param_value'] > 1 ?
                            (BaseInflector::pluralize($offer_data['param_name']) . ': ' . $offer_data['param_value']) :
                            ($offer_data['param_name'] . ' ' . $offer_data['param_value']);
                    } else {
                        $item .= $offer_data['param_name'] . ': ' . $offer_data['param_value'];
                    }
                } else {
                    $item = Yii::t('app', 'Treatment: {item}', ['item' => $list_model->inquiry->getInquiryItem()]) . '<br>';
                    $item .= Yii::t('app', 'Area: ') . $offer_data['param'] . '<br>';
                    if ($list_model->inquiry->getInquiryItem() != $offer_data['procedure_name']) {
                        $item .= Yii::t('app', 'Used brand: ') . $offer_data['procedure_name'] . '<br>';
                    }
                    $item .= (int)$offer_data['amount'] > 1 ?
                        (BaseInflector::pluralize($offer_data['param_name']) . ': ' . $offer_data['amount'] . '<br>') :
                        ($offer_data['param_name'] . ': ' . $offer_data['amount'] . '<br>');
                }
            }

            $mandrill = new Mandrill(Yii::$app->params['mandrillApiKey']);
            $patient_message = [
                'to' => [
                    [
                        'email' => $this->email,
                        'name' => $this->email,
                    ]
                ],
                "merge_language" => "mailchimp",
                "merge" => true,
                'merge_vars' => [
                    [
                        'rcpt' => $this->email,
                        'vars' => [
                            [
                                'name' => 'list_address_html',
                                'content' => getenv('ADMIN_EMAIL'),
                            ],
                            [
                                'name' => 'invoice_item',
                                'content' => $item,
                            ],
                            [
                                'name' => 'app_date',
                                'content' => date("F j, Y", strtotime($date)),
                            ],
                            [
                                'name' => 'app_time',
                                'content' => date("H:i a", strtotime($date)),
                            ],
                            [
                                'name' => 'current_year',
                                'content' => date('Y'),
                            ],
                            [
                                'name' => 'company',
                                'content' => Yii::$app->name,
                            ],
                            [
                                'name' => 'rewards',
                                'content' => (!Yii::$app->user->isGuest && Yii::$app->user->identity->userProfile->reward) ? Yii::$app->user->identity->userProfile->reward : 'no rewards',
                            ],
                            [
                                'name' => 'invoice_number',
                                'content' => $list_model->inquiry_id,
                            ],
                            [
                                'name' => 'doctor_email',
                                'content' => $doctor->user->email,
                            ],
                            [
                                'name' => 'doctor_clinic',
                                'content' => $doctor->clinic,
                            ],
                            [
                                'name' => 'price',
                                'content' => (!is_null($list_model->special_price) && !empty($list_model->special_price)) ? '$' . $list_model->special_price : '$' . $list_model->price,
                            ],
                            [
                                'name' => 'doctor_phone',
                                'content' => $doctor->profile->phone,
                            ],
                            [
                                'name' => 'doctor_website',
                                'content' => $doctor->website,
                            ],
                            [
                                'name' => 'doctor_address',
                                'content' => $doctor->profile->address . '<br>' . $doctor->profile->city . ', ' . $doctor->profile->state->short_name . ', ' . $doctor->profile->zipcode,
                            ],


                        ]
                    ]
                ],
            ];
            $doctor_message = [
                'to' => [
                    [
                        'email' => $list_model->user->email,
                        'name' => $list_model->user->email,
                    ]
                ],
                "merge_language" => "mailchimp",
                "merge" => true,
                'merge_vars' => [
                    [
                        'rcpt' => $list_model->user->email,
                        'vars' => [
                            [
                                'name' => 'list_address_html',
                                'content' => getenv('ADMIN_EMAIL'),
                            ],
                            [
                                'name' => 'company',
                                'content' => Yii::$app->name,
                            ],
                            [
                                'name' => 'app_date',
                                'content' => date("F j, Y", strtotime($date)),
                            ],
                            [
                                'name' => 'app_time',
                                'content' => date("H:i a", strtotime($date)),
                            ],
                            [
                                'name' => 'current_year',
                                'content' => date('Y'),
                            ],
                            [
                                'name' => 'patient_name',
                                'content' => $this->first_name . ' ' . strtoupper(substr($this->last_name, 0, 1)) . '.',
                            ],
                            [
                                'name' => 'invoice_number',
                                'content' => (string)$list_model->inquiry_id,
                            ],
                            [
                                'name' => 'patient_email',
                                'content' => $this->email,
                            ],
                            [
                                'name' => 'price',
                                'content' => (!is_null($list_model->special_price) && !empty($list_model->special_price)) ?  '$' . $list_model->special_price : '$' . $list_model->price,
                            ],
                            [
                                'name' => 'phone_number',
                                'content' => $this->phone_number,
                            ],
                            [
                                'name' => 'invoice_item',
                                'content' => $item,
                            ],
                            [
                                'name' => 'invoice_number',
                                'content' => $list_model->inquiry_id,
                            ],

                        ]
                    ]
                ],
            ];
            
            $mandrill->messages->sendTemplate('Patient Appointment Confirmation', [], $patient_message);
            $mandrill->messages->sendTemplate('Doctor Appointment Confirmation', [], $doctor_message);
            return true;
        }
    }
}