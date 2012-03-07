<?php

/**
 * This is the model class for table "gallery".
 *
 * The followings are the available columns in table 'gallery':
 * @property integer $id
 * @property integer $id_item
 * @property string $link
 */
class Gallery extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Gallery the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gallery';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_item, link', 'required'),
			array('id_item', 'numerical', 'integerOnly'=>true),
			array('link', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, id_item, link', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_item' => 'Id Item',
			'link' => 'Link',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('id_item',$this->id_item);
		$criteria->compare('link',$this->link,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function CopyImg($id_item, $dir, $title_item)
    {
        $dir = preg_replace( "/\D/", '' , $dir );
        $images = Yii::app()->db->createCommand()
                                  ->select('*')
                                  ->from('gallery')
                                  ->where('id_item=:id_i', array(':id_i'=>$id_item))
                                  ->queryAll();
        if(!file_exists(Yii::app()->getBasePath().'/..'.'/res/'.$dir)){
            mkdir(Yii::app()->getBasePath().'/..'.'/res/'.$dir);
            chmod(Yii::app()->getBasePath().'/..'.'/res/'.$dir, 0777);
        }
        if(!file_exists(Yii::app()->getBasePath().'/..'.'/res/'.$dir."/".$id_item)){
            mkdir(Yii::app()->getBasePath().'/..'.'/res/'.$dir."/".$id_item);
            chmod(Yii::app()->getBasePath().'/..'.'/res/'.$dir."/".$id_item, 0777);
        }
        $i = 0;
        foreach($images as $img){
              echo $img['link']."<br/>";
              copy($img['link'], $_SERVER['DOCUMENT_ROOT'].'aliex/www/res/'.$dir."/".$id_item."/".$id_item."_".$i.".jpg");
              $i++;
              set_time_limit(0);
        }

    }
}