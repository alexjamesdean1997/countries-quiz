import $ from 'jquery';
import { CryptoJSAesJson } from './CryptoJSAesJson';

function saveCorrectAnswer(countryName) {
    $.ajax({
        url: '/flag-found/' + countryName,
        method: 'POST'
    }).done(function(flagsLeft) {
        if(0 === Number(flagsLeft)){
            stopFlagGame();
        }
    });
}

function normaliseString(string) {
    return string
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replaceAll('-', ' ')
        .replaceAll('\'', ' ')
        .replaceAll('’', ' ')
        .toLowerCase();
}

function isCorrectAnswer(correctAnswer, userAnswer) {
    if (correctAnswer === userAnswer){
        return true;
    }

    let normalisedCorrectAnswer = normaliseString(correctAnswer);
    let normalisedUserAnswer    = normaliseString(userAnswer);

    return normalisedCorrectAnswer === normalisedUserAnswer;
}

$( ".name-input" ).on("input", function(){
    let encryptedAnswer = $(this).closest('.country').attr('data-encrypted');
    let userAnswer      = $(this).val();
    let decryptedAnswer = CryptoJSAesJson.decrypt(encryptedAnswer, "W0rldQu!z123");

    if (isCorrectAnswer(decryptedAnswer, userAnswer)) {
        saveCorrectAnswer(decryptedAnswer);
        $(this).closest('.country').next().find('input').focus();
        $('.countries').append($(this).closest('.country'));
        $(this).closest('.name').addClass('success').html(decryptedAnswer);
        let successCounter = $('.success-counter .successes');
        successCounter.html(parseInt(successCounter.html(), 10)+1);
    }
});

$("#stopFlagGame").click(function() {
    stopFlagGame();
});

var Interval = setInterval(startTimer, 1000);

function showAnswers() {
    $( ".country" ).each(function() {
        if (false === $(this).find('.name').hasClass('success')) {
            let encryptedAnswer = $(this).attr('data-encrypted');
            let decryptedAnswer = CryptoJSAesJson.decrypt(encryptedAnswer, "W0rldQu!z123");
            $(this).find('.name').addClass('fail').html(decryptedAnswer);
        }
    });
}

function stopFlagGame() {
    $.ajax({
        url: '/stop-flag-game',
        method: 'POST'
    }).done(function() {
        $("#stopFlagGame").hide();
        $("#restartFlagGame").show();
        clearInterval(Interval);
        showAnswers();
    });
}

function startTimer () {
    let secondsTimer = $("#gameTimerSeconds");
    let minutesTimer = $("#gameTimerMinutes");
    let hoursTimer = $("#gameTimerHours");
    let seconds = parseInt(secondsTimer.text());
    let minutes = parseInt(minutesTimer.text());
    let hours = parseInt(hoursTimer.text());

    seconds++;

    if(seconds <= 9){
        secondsTimer.text("0" + seconds);
    }

    if (seconds > 9){
        secondsTimer.text(seconds);
    }

    if (seconds > 59) {
        minutes++;
        minutesTimer.text("0" + minutes);
        secondsTimer.text("0" + 0);
    }

    if (minutes > 9){
        minutesTimer.text(minutes);
    }

    if (minutes > 59){
        hours++;
        hoursTimer.text("0" + hours);
        minutesTimer.text("0" + 0);
        secondsTimer.text("0" + 0);
        hoursTimer.show();
        $('.hours-separator').show();
    }

    if(hours <= 9){
        hoursTimer.text("0" + hours);
    }

    if (hours > 9){
        hoursTimer.text(hours);
    }
}