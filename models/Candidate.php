<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "candidate".
 *
 * @property integer $id
 * @property string $email
 * @property string $firstname
 * @property string $lastname
 * @property string $patronymic
 * @property string $birthdate
 * @property string $phone
 * @property integer $block_mailing
 */
class Candidate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'candidate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id'], 'required'],
            [['birthdate'], 'safe'],
            [['block_mailing'], 'integer'],
            [['email'], 'string', 'max' => 100],
            [['firstname', 'lastname', 'patronymic'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 20],
            [['email'], 'unique'],
            [['firstname', 'lastname', 'patronymic'], 'unique', 'targetAttribute' => ['firstname', 'lastname', 'patronymic']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => CandidateGroup::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'email' => 'Email',
            'firstname' => 'Имя',
            'lastname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'birthdate' => 'Дата рождения',
            'phone' => 'Телефон',
            'block_mailing' => 'Блокировать рассылку',
            'fullName' => 'ФИО',
            'group_id' => 'Группа',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(CandidateGroup::className(), ['id' => 'group_id']);
    }
    
    public function getFullName()
    {
        return implode(' ', [$this->lastname, $this->firstname, $this->patronymic]);
    }
    
    public static function isCandidate($params)
    {
        $res = self::find()->where([
            'email' => $params['email'], 
            'firstname' => $params['f_name'],
            'lastname' => $params['l_name'],
            'patronymic' => $params['m_name']
        ])->one();
        
        if ($res) {
            return Url::to(['/admin/candidate/view', 'id' => $res->id], true);
        }
        return false;
    }
}
