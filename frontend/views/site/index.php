<?php

/* @var $this yii\web\View */

$this->title = 'The game';
?>
<div class="site-index">
    <div class="jumbotron">
        <div class="container" id="startDiv">
            <h1>let's start the game!</h1>
            <p class="lead">Press the button to start the game.</p>
            <p><a class="btn btn-lg btn-success" href="#" id="start">Get started</a></p>
        </div>
        <div class="container" id="money" >
            <h2 class="display-4">You Won</h2>
            <button type="button" data-presentType="money" data-type="getLater" class="savePresent btn btn-primary">Get later</button>
            <button type="button" data-presentType="money" data-type="convertToPoints" class="savePresent btn btn-warning">Convert to points</button>
            <button type="button" data-presentType="money" data-type="addToBank" id="addToBank" class="btn btn-info">Replenish the bank account</button>
            <button type="button" data-presentType="money" data-type="refuseMoney" class="refuse btn btn-danger">Refuse</button>
        </div>
        <div class="container border-success" id="points">
            <h2 class="display-4">You Won</h2>
            <button type="button" data-presentType="points" data-type="topUpAccount" class="savePresent btn btn-warning">Top up account</button>
            <button type="button" data-presentType="points" data-type="refusePoints" class="refuse btn btn-danger">Refuse</button>
        </div>
        <div class="container border-success" id="thing">
            <h2 class="display-4">You Won</h2>
            <button type="button" data-presentType="thing" data-type="receiveByMail" class="savePresent btn btn-info">Receive by mail</button>
            <button type="button" data-presentType="thing" data-type="refuseThing" class="refuse btn btn-danger">Refuse</button>
        </div>
    </div>
    <div id="userEmail" data-userEmail="<?=Yii::$app->user->identity->email?>"></div>
    <div id="userId" data-userId="<?=Yii::$app->user->identity->id?>"></div>
    <div id="csrfToken" data-token="<?=Yii::$app->request->getCsrfToken()?>""></div>
</div>
