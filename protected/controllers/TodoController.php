<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthController
 *
 * @author Alex
 */
class TodoController extends CAjaxJsonController
{

    public function actionGetList()
    {
        $shared = CApp::getInstance()->request->getParam('shared', 0);
        $criteria = new CDbSelectionCriteria();
        if ($shared) {

        }
        $criteria->compare('owner', Users::getCurrent()->id);
        $todosList = Todos::model()->findAll($criteria);
        $list = [];
        $allDone = 1;
        foreach ($todosList as $todo) {
            $list[] = $todo->getAttributes();
            if (!$todo->done) {
                $allDone = 0;
            }
        }
        $this->responce->setCustom([
            'list' => $list,
            'login' => Users::getCurrent()->login,
            'allDone' => $allDone,
        ]);
    }

    /**
     * 
     * @return Todos
     * @throws Exception
     */
    protected function getTask()
    {
        $taskData = CApp::getInstance()->request->getParam('todoData', []);
        if (empty($taskData)) {
            throw new Exception("No data passed!", 21);
        }
        if (!empty($taskData['id'])) {
            $task = Todos::model()->findByAttributes(['id' => $taskData['id']]);
        }
        if (empty($task)) {
            $task = Todos::model();
        }
        $task->setAttributes($taskData);
        return $task;
    }

    public function actionCreateOrUpdate()
    {
        $task = $this->getTask();
        $task->save();
        $this->responce->setCustom([
            'todo' => $task->getAttributes(),
        ]);
    }

    public function actionClearOne()
    {
        $task = $this->getTask();
        $task->delete();
    }

    public function actionCheckAll()
    {
        $done = CApp::getInstance()->request->getParam('done', NULL);
        if ($done == NULL) {
            throw new Exception("You must tell state!", 31);
        }
        $tasks = Todos::model()->findAllByAttributes([
            'owner' => Users::getCurrent()->id,
        ]);
        foreach ($tasks as $task) {
            $task->done = $done;
            $task->save();
        }
    }

    public function actionClearAllDone()
    {
        $tasks = Todos::model()->findAllByAttributes([
            'owner' => Users::getCurrent()->id,
            'done' => 1,
        ]);

        if (!empty($tasks)) {
            foreach ($tasks as $task) {
                $task->delete();
            }
        }
    }
}