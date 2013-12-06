<?php

namespace amnah\yii2\user\models\search;

use amnah\yii2\user\models\Profile;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use amnah\yii2\user\models\User;

/**
 * UserSearch represents the model behind the search form about User.
 */
class UserSearch extends Model
{
	public $id;
	public $role_id;
	public $email;
	public $new_email;
	public $username;
	public $password;
	public $status;
	public $auth_key;
	public $create_time;
	public $update_time;
	public $ban_time;
	public $ban_reason;
    public $full_name;

	public function rules()
	{
		return [
			[['id', 'role_id', 'status'], 'integer'],
			[['email', 'new_email', 'username', 'password', 'auth_key', 'create_time', 'update_time', 'ban_time', 'ban_reason', 'full_name'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'role_id' => 'Role ID',
			'email' => 'Email',
			'new_email' => 'New Email',
			'username' => 'Usernadsadame',
			'password' => 'Password',
			'status' => 'Status',
			'auth_key' => 'Auth Key',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'ban_time' => 'Ban Time',
			'ban_reason' => 'Ban Reason',
			'full_name' => 'Full Name',
		];
	}

	public function search($params)
	{
        // set up query with eager innerJoin on profile data
        $query = User::find();
        $userTable = User::tableName();
        $profileTable = Profile::tableName();
        $query->innerJoin($profileTable, "$userTable.id=$profileTable.user_id");
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // add extra sort attributes
        $addSortAttributes = ["full_name"];
        foreach ($addSortAttributes as $addSortAttribute) {
            $dataProvider->sort->attributes[$addSortAttribute] = [
                'asc' => [$addSortAttribute => SORT_ASC],
                'desc' => [$addSortAttribute => SORT_DESC],
                'label' => $this->getAttributeLabel($addSortAttribute),
            ];
        }

		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}

		$this->addCondition($query, 'id');
		$this->addCondition($query, 'role_id');
		$this->addCondition($query, 'email', true);
		$this->addCondition($query, 'new_email', true);
		$this->addCondition($query, 'username', true);
		$this->addCondition($query, 'password', true);
		$this->addCondition($query, 'status');
		$this->addCondition($query, 'auth_key', true);
		$this->addCondition($query, 'create_time', true);
		$this->addCondition($query, 'update_time', true);
		$this->addCondition($query, 'ban_time', true);
		$this->addCondition($query, 'ban_reason', true);
		$this->addCondition($query, 'full_name', true);

		return $dataProvider;
	}

	protected function addCondition($query, $attribute, $partialMatch = false)
	{
		$value = $this->$attribute;
		if (trim($value) === '') {
			return;
		}

        /** @var ActiveQuery $query */
		if ($partialMatch) {
			$value = '%' . strtr($value, ['%'=>'\%', '_'=>'\_', '\\'=>'\\\\']) . '%';
			$query->andWhere(['like', $attribute, $value]);
		} else {
			$query->andWhere([$attribute => $value]);
		}
	}
}
