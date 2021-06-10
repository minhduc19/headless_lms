<div class="wrap-item">
<?php
if(isset($items) && !empty($items)){
   foreach ($items as $key => $value) {
       ?>
    <div class="item-game">
        <a href="<?php echo $value['Game']['play_url'] . '?game_id=' . $value['Game']['id'] . '&api_key=' . $api_key . '&access_token=' . $access_token;?>">
            <div class="image" style="background-image: url(<?php echo $value['Game']['icon'];?>)"></div>
            <div class="name-game"><?php echo $value['Game']['name'];?></div>
        </a>
    </div>
<?php
   }
}
?>
</div>
<style>
    .item-game{
        border: 0.1rem solid #62c2ff;
        margin: 2rem 0 0 2rem;
        width: calc(100% / 6 - 2rem);
    }
    @media (max-width: 576px){
        .item-game {
            width: calc(100% / 2 - 1.5rem);
        }
    }
    .image{
            height: 14rem;
            background-repeat: no-repeat;
            background-size: 50%;
            background-position: 50%;
            background-color: #000;
    }
    .name-game{
        height: 4rem;
        text-align: center;
        padding: 0 0.5rem;
        line-height: 4rem;
        color: #fff;
        background: url(/img/images/bg-name.png) no-repeat top center / cover;
        font-family: 'Helvetica-Neue';
        font-size: 1.4rem;
        -o-text-overflow: ellipsis;
        text-overflow: ellipsis;
        overflow-y: hidden;
        white-space: nowrap;
    }
</style>