var crypto = require("crypto");

function t(params) {
    var options = {
        url: 'https://dev.getreferralmd.com/v1/crm/contacts',
        jar: false,
        json: {
            firstName: 'firstName',
            lastName: 'lastName',
            jobTitle: 'jobTitle',
            contact_type: 'contact_type',
            contact_role: 'contact_role'
        },
        headers: {
            'X-XSRF-TOKEN': '',
            'API_KEY': params,
            'API_SIGNATURE': ''
        }
    };

    var payload = calculateHash(params, 'POST', '/v1/crm/contacts', JSON.stringify(options.json));

    options.headers.API_SIGNATURE = payload

    console.log(options)
}

var calculateHash = (secret, method, url, body) => {
    var timestring = (new Date()).toISOString().split('.')[0];
    var timestamp = timestring.substring(0, timestring.length - 3) + "Z";
    var payload = timestamp + method + url + body;
    var computedSignature = crypto.createHmac("sha256", secret).update(payload).digest("hex");
    return computedSignature;
};

t('707b000bc175cf58cd6aefb002a9d959')

/*

    testPost: function (params, callback) {

        var options = {
            url: data.baseURL + '/v1/crm/contacts',
            jar: false,
            json: {
                firstName: 'Sam',
                lastName: 'McGunner',
                jobTitle: 'Coordinator',
                contact_type: 'user',
                contact_role: 'staff'
            },
            headers: {
                'X-XSRF-TOKEN': '',
                'API_KEY': params.hmacKey
            }
        };

        var payload = calculateHash(params.hmacSecret, 'POST', '/v1/crm/contacts', JSON.stringify(options.json));

        options.headers.API_SIGNATURE = payload

        it('should add a contact, using hmac api', function (done) {
            request.post(options, function (error, response, body) {
                expect(error).to.not.exist;
                expect(response.statusCode).to.equal(200);
                done(callback(params));
            });
        });
    }

    var calculateHash = (secret, method, url, body) => {
        var timestring = (new Date()).toISOString().split('.')[0];
        var timestamp = timestring.substring(0, timestring.length - 3) + "Z";
        var payload = timestamp + method + url + body;
        var computedSignature = crypto.createHmac("sha256", secret).update(payload).digest("hex");
        return computedSignature;
    };
    */