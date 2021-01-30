<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%server_monitoring}}".
 *
 * @property int $id
 * @property int $server
 * @property string $time
 * @property int $online
 * @property string $date
 */
class ServerMonitoring extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%server_monitoring}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['server', 'time', 'online', 'date'], 'required'],
            [['server', 'online'], 'integer'],
            [['time', 'date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'server' => 'Server',
            'time' => 'Time',
            'online' => 'Online',
            'date' => 'Date',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \backend\models\query\ServerMonitoringQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \backend\models\query\ServerMonitoringQuery(get_called_class());
    }
}
