import $ from 'jquery';

export function startGameTimer () {
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