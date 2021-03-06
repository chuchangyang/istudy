<?php

namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AjaxFilter;
use yii\base\Model;
use backend\form\CustomerSearch;
use common\models\Customer;
use common\models\CustomerProfile;

class CustomerController extends Controller
{


    public function rbac()
    {
        return [
           'class' => 'yii\filters\AccessControl',
           'ruleConfig' => [
                'class' => 'yii\filters\AccessRule',
                'allow' => true,
           ],
           'rules' => [
                [
                   'actions' => ['index', 'view'],
                   'roles' => ['customer/view'],
                ],
                [
                   'actions' => ['create'],
                   'roles' => ['customer/create'],
                ],
                [
                   'actions' => ['update'],
                   'roles' => ['customer/update'],
                ],
                [
                   'actions' => ['delete'],
                   'roles' => ['customer/delete'],
                ],
           ],

        ];
    }

    public function actionIndex()
    {
        $search = new CustomerSearch();
        $provider = $search->search(Yii::$app->request->get());

        return $this->render('index', [
            'searchModel'  => $search,
            'dataProvider' => $provider,
        ]);
    }


    public function actionCreate()
    {
        $this->_title('Create customer');
        $customer = new Customer([ 
            'scenario' => Customer::SCENARIO_CREATE,
        ]);
        $profile = new CustomerProfile([
            'scenario' => CustomerProfile::SCENARIO_CREATE, 
        ]);
        if($post = $this->post) {
            $success = $customer->load($post) &&
                 $profile->load($post) &&
                 $customer->validate() &&
                 $profile->validate();
            if($success) {
                 Customer::getDb()->transaction(function() use ($customer, $profile) {
                     $customer->save(false);
                     $profile->setCustomer($customer);
                     $profile->save(false);
                 });
                 return $this->redirect(['index']);
            }
        }
        $customer->cleanPassword();
        return $this->render('edit', [
            'customer' => $customer,
            'profile'  => $profile,
        ]);
    }



    public function actionView($id)
    {
        $customer = $this->findCustomer($id);
        return $this->asJson($customer->toArray());
    }




    public function actionUpdate($id)
    {
        $this->_title('Update customer');
        $customer = $this->findCustomer($id);
        $profile = $customer->profile;
        $customer->scenario = Customer::SCENARIO_UPDATE;
        $profile->scenario = CustomerProfile::SCENARIO_UPDATE;

        if($post = $this->post) {
            $success = $customer->load($post) &&
                $profile->load($post) &&
                $customer->validate() &&
                $profile->validate();
            if($success) {
                Customer::getDb()->transaction(function() use ($customer, $profile) {
                    $customer->save(false);
                    $profile->setCustomer($customer);
                    $profile->save(false);
                });
                return $this->redirect(['index']);
            }
        }
        $customer->cleanPassword();
        return $this->render('edit', [
            'customer' => $customer,
            'profile'  => $profile,
        ]);       
    }

    public function actionDelete($id)
    {
        $customer = $this->findCustomer($id);
        Customer::getDb()->transaction(function() use ($customer) {
            if($customer->profile) {
                $customer->profile->delete();
            }
            $customer->delete();
        });
        return $this->redirect(['index']);
    }


    public function findCustomer($id)
    {
        return $this->findModel($id, Customer::className(), function($query) {
             $query->with('profile');
        }); 
    }

}