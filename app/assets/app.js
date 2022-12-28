import './styles/app.scss';
import './bootstrap';
import $ from 'jquery';
import * as CryptoJS from 'crypto-js';

var CryptoJSAesJson = {
    /**
     * Encrypt any value
     * @param {*} value
     * @param {string} password
     * @return {string}
     */
    encrypt: function (value, password) {
        return CryptoJS.AES.encrypt(JSON.stringify(value), password, { format: CryptoJSAesJson }).toString()
    },
    /**
     * Decrypt a previously encrypted value
     * @param {string} jsonStr
     * @param {string} password
     * @return {*}
     */
    decrypt: function (jsonStr, password) {
        return JSON.parse(CryptoJS.AES.decrypt(jsonStr, password, { format: CryptoJSAesJson }).toString(CryptoJS.enc.Utf8))
    },
    /**
     * Stringify cryptojs data
     * @param {Object} cipherParams
     * @return {string}
     */
    stringify: function (cipherParams) {
        var j = { ct: cipherParams.ciphertext.toString(CryptoJS.enc.Base64) }
        if (cipherParams.iv) j.iv = cipherParams.iv.toString()
        if (cipherParams.salt) j.s = cipherParams.salt.toString()
        return JSON.stringify(j).replace(/\s/g, '')
    },
    /**
     * Parse cryptojs data
     * @param {string} jsonStr
     * @return {*}
     */
    parse: function (jsonStr) {
        var j = JSON.parse(jsonStr)
        var cipherParams = CryptoJS.lib.CipherParams.create({ ciphertext: CryptoJS.enc.Base64.parse(j.ct) })
        if (j.iv) cipherParams.iv = CryptoJS.enc.Hex.parse(j.iv)
        if (j.s) cipherParams.salt = CryptoJS.enc.Hex.parse(j.s)
        return cipherParams
    }
}

function isCorrectAnswer(correctAnswer, userAnswer) {
    if (correctAnswer === userAnswer){
        return true;
    }

    let normalisedCorrectAnswer = correctAnswer.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace('-', ' ').toLowerCase();
    let normalisedUserAnswer    = userAnswer.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace('-', ' ').toLowerCase();

    return normalisedCorrectAnswer === normalisedUserAnswer;
}

$( ".name-input" ).on("input", function(){
    let encryptedAnswer = $(this).closest('.country').attr('data-encrypted');
    let userAnswer      = $(this).val();
    let decryptedAnswer = CryptoJSAesJson.decrypt(encryptedAnswer, "W0rldQu!z123");

    if (isCorrectAnswer(decryptedAnswer, userAnswer)) {
        $('.countries').append($(this).closest('.country'));
        $(this).closest('.name').addClass('success').html(decryptedAnswer);
        let successCounter = $('.success-counter .successes');
        successCounter.html(parseInt(successCounter.html(), 10)+1);
    }
});