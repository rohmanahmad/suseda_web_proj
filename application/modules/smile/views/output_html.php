<html>
    <head>
        <title><?=$title?></title>
        <style>
            div{
                margin:5px;
            }
            input{
                height:45px;
                font-size:24px;
            }
            .left{
                position:relative;
                float:left;
                margin-top:0px;
            }
            .big{
                border:1px solid #999;
                padding: 5px;
            }
            .buble{
                background-image:url('<?=base_url();?>images/icons/status_bg.png');
                background-repeat:no-repeat;
                width:280px;
                height:200px;
                left:-100px;
                top:70px;
                z-index:5;
                padding: 70px 0 0 20px;
            }
        </style>
        <?=smiley_js();?>
    </head>
    <body>
        <?=$body?>
    </body>
</html>