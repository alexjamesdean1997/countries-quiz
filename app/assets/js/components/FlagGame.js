import $ from 'jquery';
import { cryptoJSAesJson } from './CryptoJSAesJson';
import { incrementScore } from './ScoreManager';
import { startGameTimer } from './GameTimer';
import { stringNormalizer } from './StringNormalizer'

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

function isCorrectAnswer(correctAnswer, userAnswer) {
    let normalisedCorrectAnswer = stringNormalizer(correctAnswer);
    let normalisedUserAnswer    = stringNormalizer(userAnswer);

    return normalisedCorrectAnswer === normalisedUserAnswer;
}

function showAnswers() {
    $( ".country" ).each(function() {
        if (false === $(this).find('.name').hasClass('success')) {
            let encryptedAnswer = $(this).attr('data-encrypted');
            let decryptedAnswer = cryptoJSAesJson.decrypt(encryptedAnswer, "W0rldQu!z123");
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

var Interval = setInterval(startGameTimer, 1000);

$( ".name-input" ).on("input", function(){
    let encryptedAnswer = $(this).closest('.country').attr('data-encrypted');
    let decryptedAnswer = cryptoJSAesJson.decrypt(encryptedAnswer, "W0rldQu!z123");
    let userAnswer      = $(this).val();

    if (isCorrectAnswer(decryptedAnswer, userAnswer)) {
        saveCorrectAnswer(decryptedAnswer);
        $(this).closest('.country').next().find('input').focus();
        $('.countries').append($(this).closest('.country'));
        $(this).closest('.name').addClass('success').html(decryptedAnswer);
        incrementScore();
    }
});

$("#stopFlagGame").click(function() {
    stopFlagGame();
});