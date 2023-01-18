import $ from 'jquery';

export function incrementScore() {
    let successCounter = $('.success-counter .successes');
    successCounter.html(parseInt(successCounter.html(), 10)+1);
}