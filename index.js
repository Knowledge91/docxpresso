const hmacsha1 = require('hmacsha1');
const uuidv4 = require('uuid/v4');
const sha1 = require('js-sha1');
const Base64 = require('js-base64').Base64;
const hex2bin = require('locutus/php/strings/hex2bin');
const crypto = require('crypto');

const masterKey = process.env.APIKEY;
const docxpressoInstallation = 'https://cysae.a.docxpresso.com';

function returnLink(url, id, opt) {
    const uuid = uuidv4().replace(/-/g, '');
    const timestamp = Math.floor(new Date().getTime() / 1000);

    let control = ''
    control += id + '-';
    control += timestamp + '-' + uuid;
    control += '-' + opt;

    const dataKey = crypto.createHash('sha1').update(control).digest();
    const apiKey = crypto.createHmac('sha1', masterKey).update(dataKey).digest('hex');
    console.log(apiKey);

    let addr = `${url}?uniqid=${uuid}&timestamp=${timestamp}&APIKEY=${apiKey}&options=${opt}`;

    return addr;
}


export function previewDocument(id) {
    let url = `${docxpressoInstallation}/documents/preview/${id}`;

    const opt = {
        'format': 'odt'
    }

    const optBase64 = Base64.encodeURI(JSON.stringify(opt));
    return returnLink(url, id, optBase64+',,');
}
