<?php

namespace Automation;

defined('ABSPATH') || exit;
?>
<style>
    #atm-box {
        background: linear-gradient(135deg,rgb(40,38,97) 0%,rgb(40,116,252) 100%);
        line-height: normal;
        padding: 4rem 2rem;
        border-radius: 1rem;
        margin: 2rem;
    }
    #atm-title {
        font-size: 5rem;
        color: #fff;
        line-height: normal;
        text-align: center;
        font-weight: bold;
    }
    #atm-description {
        font-size: 3rem;
        color: #ddd;
        line-height: normal;
        text-align: center;
        font-weight: bold;
    }

    #atm-description li {
        color: yellow;
        text-align: center;
        font-size: 1.2em;
    }

    #atm-features {
        display: flex;
        font-size: 1.5rem;
        color: #666;
        line-height: normal;
        text-align: left;
        font-weight: bold;
        width: 100%
    }

    #atm-features div {
        padding: 2rem;
        flex-grow: 1;
        border: 1px solid #eee;
        box-shadow: 0 0 5px #eee;
        margin: 2rem;
        border-radius: 1rem;
    }

    #atm-features h3 {
        font-size: 2rem;
        margin-top: 0;
        margin-bottom: 1rem;
        color: #000;
    }

    #atm-features p {
        font-size: 1.1rem;
        margin-top: 0;
        margin-bottom: 1rem;
        color: #666;
    }

    #atm-features a {
        font-size: 1.3rem;
    }

    #atm-developers {

    }

    .ms-slider {
        display: inline-block;
        height: 1.5em;
        overflow: hidden;
        vertical-align: middle;
        mask-image: linear-gradient(transparent, white, white, white, transparent);
        mask-type: luminance;
        mask-mode: alpha;
    }
    .ms-slider__words {
        display: inline-block;
        margin: 0;
        padding: 0;
        list-style: none;
        animation-name: wordSlider;
        animation-timing-function: ease-out;
        animation-iteration-count: infinite;
        animation-duration: 7s;
    }
    .ms-slider__word {
        display: block;
        line-height: 1.3em;
        text-align: left;
    }
    @keyframes wordSlider {
        0%, 27% {
            transform: translateY(0%);
        }
        33%, 60% {
            transform: translateY(-25%);
        }
        66%, 93% {
            transform: translateY(-50%);
        }
        100% {
            transform: translateY(-75%);
        }
    }

</style>
<?php include AUTOMATION_DIR . '/admin/menu.php' ?>
<div id="atm-box">
    <div id="atm-title">Welcome Emails</div>
    <div id="atm-description">
        Send <span style="color: #fff">wonderful</span> emails to impress your<br>
        <div class="ms-slider">
            <ul class="ms-slider__words">
                <li class="ms-slider__word">readers</li>
                <li class="ms-slider__word">customers</li>
                <li class="ms-slider__word">competitors</li>
                <!-- This last word needs to duplicate the first one to ensure a smooth infinite animation -->
                <li class="ms-slider__word">readers</li>
            </ul>
        </div>
    </div>
</div>
<div id="atm-features">
    <div id="atm-owner">
        <h3>Configure your forms</h3>
        <p>Jump start directly adding welcome emails to your forms.</p>
        <p>
            <a href="?page=automation_types" class="button-primary">Bring me there</a>
        </p>
    </div>

    <div id="atm-developers">
        <h3>Twick the settings</h3>
        <p>
            Customize the sender name and the sender email.
        </p>
        <p>
            <a href="?page=automation_settings" class="button-primary">Move there</a>
        </p>
    </div>
</div>



