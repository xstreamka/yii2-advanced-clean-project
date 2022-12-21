<?php
/**
 * Created by PhpStorm.
 * User: p.durtsev
 * Date: 19.08.2022
 * Time: 10:13
 */

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
    public $group;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'name', 'surname', 'email'], 'filter', 'filter' => 'trim'],

            [['id', 'status'], 'integer'],
            [['username', 'name', 'surname', 'email', 'group', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();

        $query->joinWith('authAssignment')->distinct();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user.id' => $this->id,
            'user.status' => $this->status,
            'auth_assignment.item_name' => $this->group
        ]);

        $query
            ->andFilterWhere(['ilike', 'user.username', $this->username])
            ->andFilterWhere(['ilike', 'user.name', $this->name])
            ->andFilterWhere(['ilike', 'user.surname', $this->surname])
            ->andFilterWhere(['like', 'user.email', $this->email]);

        if (!Yii::$app->user->isSuperadmin()) {
            $query->andFilterWhere(['not', ['auth_assignment.item_name' => 'superadmin']]);
        }

        if (!empty($this->created_at)) {
            $time = strtotime($this->created_at);
            $query
                ->andFilterWhere(['>=', 'user.created_at', date('Y-m-d 00:00:00', $time)])
                ->andFilterWhere(['<=', 'user.created_at', date('Y-m-d 23:59:59', $time)]);
        }

        if (empty($params['sort'])) {
            $query->orderBy(['user.created_at' => SORT_ASC]);
        }

        return $dataProvider;
    }
}