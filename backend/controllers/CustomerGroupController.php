<?php

namespace backend\controllers;

use Yii;
use common\models\CustomerGroup;
use backend\form\CustomerGroupSearch;

class CustomerGroupController extends Controller
{

    public function actionIndex()
    {
    	$filterModel = new CustomerGroupSearch();
        $dataProvider = $filterModel->search(Yii::$app->request->get());
        return $this->render('index', [
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
        ]);
    }



    public function actionCreate()
    {
        $group = new CustomerGroup([
            'scenario' => CustomerGroup::SCENARIO_CREATE,
        ]);
        if($post = $this->post) {
            if($group->load($post) && $group->validate()) {
                CustomerGroup::getDb()->transaction(function() use ($group) {
                	if($group->isAttributeChanged('is_default') && $group->is_default) {
                		CustomerGroup::updateAll(
                			['is_default' => 0], 
                			['is_default' => 1]
                		);
                	}
                    $group->save(false);
                });
                return $this->redirect(['index']);
            }
        }
        return $this->render('edit', [
            'group' => $group,
        ]);
    }

    public function actionUpdate($id)
    {
        $group = $this->findGroup($id);
        $group->scenario = CustomerGroup::SCENARIO_UPDATE;
        if($post = $this->post) {
        	if($group->load($post) && $group->validate()) {
        		CustomerGroup::getDb()->transaction(function() use ($group){
                    if($group->isAttributeChanged('is_default') && $group->is_default) {
                    	CustomerGroup::updateAll(
                    		['is_default' => 0],
                    		['is_default' => 1]
                    	);
                    	$group->save(false);
                    }
        		});
        		return $this->redirect(['index']);
        	}
        }
        return $this->render('edit', [
            'group' => $group,
        ]);
    }


    public function actionView($id)
    {
        $group = $this->findGroup($id);
        return $this->asJson($group);
    }


    public function actionDelete($id)
    {
        $group = $this->findGroup($id);
        if($group->canDelete()) {
        	$group->delete();
        }
        return $this->redirect(['index']);
    }

    public function findGroup($id)
    {
         return $this->findModel($id, CustomerGroup::className());
    }

}