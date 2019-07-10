<?php

/* @var $this yii\web\View */

$this->title = 'The game';
?>
<div class="site-index">

    <div class="jumbotron">
        <div class="container" id="startDiv">
            <h1>let's start the game!</h1>

            <p class="lead">Press the button to start the game.</p>

            <p><a class="btn btn-lg btn-success" href="#" id="start" data-token="<?=Yii::$app->request->getCsrfToken()?>">Get started</a></p>
        </div>

        <div class="container" id="money">
            <h2 class="display-4">You Won</h2>

            <button type="button" data-type="getLater" class="money btn btn-primary">Get later</button>
            <button type="button" data-type="convertToPoints" class="money btn btn-warning">Convert to points</button>
            <button type="button" data-type="addToBank" class="money btn btn-info">Replenish the bank account</button>
            <button type="button" data-type="refuseMoney" class="money btn btn-danger">Refuse</button>
        </div>
        <div class="container border-success" id="points">
            <h2 class="display-4">You Won</h2>

            <button type="button" data-type="topUpAccount" class="points btn btn-warning">Top up account</button>
            <button type="button" data-type="refusePoints" class="points btn btn-danger">Refuse</button>
        </div>
        <div class="container border-success" id="thing">
            <h2 class="display-4">You Won</h2>

            <button type="button" data-type="receiveByMail" class="thing btn btn-info">Receive by mail</button>
            <button type="button" data-type="refuseThing" class="thing btn btn-danger">Refuse</button>
        </div>
    </div>

</div>
