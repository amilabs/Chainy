// ConsenSys Truffle contract test

contract('Chainy', function(accounts){

    //nohup testrpc --account="0x967a7b8992be2482650fc470fc28bee8aab5f71c23a05db26e15878ad600af72, 81408483200000000000" --account="0x202ad4c87a83317e8180f88255b4017c65cde113b0314807bba0802758317134, 97538264000000000000" --account="0xe7b4cb45748ef5f3c953dbce5097b4ad84aec901d1613266a84a252af87953c3, 100000000000000000000" --account="0x72714ddb2ccad9c0d4c64cb645a6cff299b0c3eb8726c8a015c2b6c0fb4badd1, 100000000000000000000" --account="0x3f7a166c8e07f3731ea298642189a76ffbe9e5978852684f2e9d29bf194efae4, 100000000000000000000" --account="0x94a61afbe400c53b2bc30cd88fb3899d37afbf8e9a424fbfbaad26a2a397ba03, 100000000000000000000" --account="0x02ab91143f14eaaed7a84e61a51e7abb6f5c876dbcaeda580736a3b1f1c0bfad, 2000000000000000" --account="0x00d6b4b7ca5b6bb394bb96a5fb7ee52c820fab2a71108f45dbe3e9ce047bb19a, 4000000000000000" --account="0xe53790d832901b920328fb0c225644ba6037b7a304f6031aec33c35635f70486, 78765137500000000000" --account="0x4164bb78e401a6b045139dc76890cd436df0f65be81f0bc002740d1133edf7c8, 100000000000000000000" > /var/log/testrpc.log 2>&1 &

    // owner = accounts[0]
    // not_owner = accounts[8]
    // 0.002 balance = accounts[6]
    // 0.004 balance = accounts[7]

    var clog = function(message){
        if(1){
            console.log(message);
        }
    }

    var int2base58 = function(value){
        var alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ',
            base = alphabet.length;
        if(typeof(value) !== 'number' || value !== parseInt(value)){
            clog('"int2base58" only accepts integers');
            return null;
        }
        var encoded = '';
        while(value){
            var remainder = value % base;
            value = Math.floor(value / base);
            encoded = alphabet[remainder].toString() + encoded;        
        }
        return encoded;
    }

    var aTests = [
        // config
        {
            'contract': 'Chainy',
            'method'  : 'setConfig',
            'tests'   : [
                {
                    'params': ['ne|fee', '0'],
                    'from'  : 'not_owner',
                    'result': 'fail'
                },
                {
                    'params': ['ne|fee', '5230000000000000'],
                    'from'  : 'owner',
                    'result': 'success'
                },
                {
                    'params': ['ne|blockoffset', '0'],
                    'from'  : 'owner',
                    'result': 'success'
                },
            ],
        },
        {
            'contract': 'Chainy',
            'method'  : 'getConfig',
            'type'    : 'call',
            'tests'   : [
                {
                    'params': ['ne|fee'],
                    'from'  : 'not_owner',
                    'result': '5230000000000000'
                },
                {
                    'params': ['ne|fee'],
                    'from'  : 'owner',
                    'result': '5230000000000000'
                },
            ],
        },
        {
            'contract': 'Chainy',
            'method'  : 'setConfig',
            'tests'   : [
                {
                    'params': ['ne|fee', '0'],
                    'from'  : 'owner',
                    'result': 'success'
                }
            ],
        },
        // setServiceAccount
        {
            'contract': 'Chainy',
            'method'  : 'setServiceAccount',
            'tests'   : [
                {
                    'params': ['accounts[1]', 'true'],
                    'from'  : 'not_owner',
                    'result': 'fail'
                },
                {
                    'params': ['accounts[1]', 'false'],
                    'from'  : 'owner',
                    'result': 'success'
                },
                {
                    'params': ['accounts[2]', 'true'],
                    'from'  : 'owner',
                    'result': 'success'
                },
            ],
        },
        // setReceiverAddress
        {
            'contract': 'Chainy',
            'method'  : 'setReceiverAddress',
            'tests'   : [
                {
                    'params': ['accounts[1]'],
                    'from'  : 'not_owner',
                    'result': 'fail'
                },
                {
                    'params': ['accounts[4]'],
                    'from'  : 'owner',
                    'result': 'success'
                },
            ],
        },
        // set fee to 5000000000000001
        {
            'contract': 'Chainy',
            'method'  : 'setConfig',
            'tests'   : [
                {
                    'params': ['ne|fee', '5000000000000001'],
                    'from'  : 'owner',
                    'result': 'success'
                }
            ],
        },
        // add Chainy data
        {
            'contract': 'Chainy',
            'method'  : 'addChainyData',
            'tests'   : [
                {
                    'params': ['ne|{"id":"CHAINY", version: 1, type: "L", url: "http:\/\/site.com\/file.zip", hash: "24356450ab5de1cf7b07a85cda0ff91c0b44a347b731402c4fa6729ec7c98", filetype: "arc", filesize: 1024, description: "reports.everex.one"}'],
                    'from'  : 'accounts[8]',
                    'value' : '5000000000000000',
                    'result': 'fail'
                }
            ],
        },
        {
            'contract': 'Chainy',
            'method'  : 'addChainyData',
            'tests'   : [
                {
                    'params': ['ne|{"id":"CHAINY", version: 1, type: "L", url: "http:\/\/site.com\/file.zip", hash: "24356450ab5de1cf7b07a85cda0ff91c0b44a347b731402c4fa6729ec7c98", filetype: "arc", filesize: 1024, description: "reports.everex.one"}'],
                    'from'  : 'accounts[8]',
                    'value' : '5000000000000001',
                    'result': 'success'
                }
            ],
        },
        // change owner
        {
            'contract': 'Chainy',
            'method'  : 'transferOwnership',
            'tests'   : [
                {
                    'params': ['accounts[5]'],
                    'from'  : 'owner',
                    'result': 'success'
                }
            ],
        },
        {
            'contract': 'Chainy',
            'method'  : 'addChainyData',
            'tests'   : [
                {
                    'params': ['ne|{"id":"CHAINY", version: 1, type: "L", url: "http:\/\/site.com\/file.zip", hash: "24356450ab5de1cf7b07a85cda0ff91c0b44a347b731402c4fa6729ec7c98", filetype: "arc", filesize: 1024, description: "reports.everex.one"}'],
                    'from'  : 'accounts[8]',
                    // value = fee + 3000000000000000
                    'value' : '8000000000000001',
                    'result': 'success'
                }
            ],
        },
        {
            'contract': 'Chainy',
            'method'  : 'releaseFunds',
            'tests'   : [
                {
                    'params': [],
                    'from'  : 'not_owner',
                    'result': 'fail'
                },
                {
                    'params': [],
                    // 2852200000000000 gas used
                    'from'  : 'accounts[5]',
                    'result': 'success'
                }
            ],
        },
        {
            'contract': 'Chainy',
            'method'  : 'transferOwnership',
            'tests'   : [
                {
                    'params': ['accounts[0]'],
                    // 2821800000000000 gas used
                    'from'  : 'accounts[5]',
                    'result': 'success'
                }
            ],
        },
        {
            'contract': 'Chainy',
            'method'  : 'setConfig',
            'tests'   : [
                {
                    'params': ['ne|fee', '0'],
                    'from'  : 'owner',
                    'result': 'success'
                }
            ],
        },
        {
            'contract': 'Chainy',
            'method'  : 'getChainyURL',
            'type'    : 'call',
            'tests'   : [
                {
                    'params': [],
                    'from'  : 'not_owner',
                    'result': 'https://txn.me/'
                },
                {
                    'params': [],
                    'from'  : 'owner',
                    'result': 'https://txn.me/'
                }
            ],
        },
        {
            'contract': 'Chainy',
            'method'  : 'setChainyURL',
            'tests'   : [
                {
                    'params': ['ne|https://new-txn.com/'],
                    'from'  : 'not_owner',
                    'result': 'fail'
                },
                {
                    'params': ['ne|https://new-txn-site.io/'],
                    'from'  : 'owner',
                    'result': 'success'
                }
            ],
        },
    ];

    it("/Should check initial balance of receiver address/", function(){
        clog(web3.eth.getBalance(accounts[4]));
        assert.equal(web3.eth.getBalance(accounts[4]), 100000000000000000000, '');
    });

    it("/Should check initial balance of owner (accounts[5])/", function(){
        clog(web3.eth.getBalance(accounts[5]));
        assert.equal(web3.eth.getBalance(accounts[5]), 100000000000000000000, '');
    });

    aTests.forEach(function(oTest){
        oTest.tests.forEach(function(curTest){

            oTest.name = oTest.contract + "." + oTest.method + (curTest.err ? (' (' + curTest.err + ')') : '') + " from " + curTest.from + " should " + curTest.result + ", params: " + JSON.stringify(curTest.params);

            it('/' + oTest.name + '/', function(){
                //clog('Test: ' + JSON.stringify(curTest));

                var chainy = Chainy.deployed();

                var result = null;
                var from;
                if(curTest.from.substring(0, 8) == 'accounts'){
                    from = eval(curTest.from);
                }else{
                    from = ((curTest.from == 'owner') ? accounts[0] : accounts[8]);
                }
                //clog('From: ' + from);
                var aParams = [];
                if('undefined' === typeof(oTest.type)){
                    oTest.type = 'tx';
                }

                var callMethod = 'chainy.' + oTest.method + (oTest.type === 'call' ? '.call' : '') + '(';
                //clog('Params:');
                for(var i = 0; i < curTest.params.length; i++){
                    if(curTest.params[i].substring(0, 3) == 'ne|'){
                        aParams.push(curTest.params[i].substring(3));
                        //clog(curTest.params[i].substring(3));
                    }else{
                        aParams.push(eval(curTest.params[i]));
                        clog(eval(curTest.params[i]));
                    }
                    //clog(aParams[i]);
                    if(i > 0){
                        callMethod += ',';
                    }
                    callMethod += 'aParams[' + i + ']';
                }
                if(curTest.params.length > 0) callMethod += ',';
                callMethod += '{from: from' + (curTest.value ? (',value: ' + curTest.value) : '') + '})';
                clog('Contract method = ' + callMethod);

                return eval(callMethod)
                .then(function(res){
                    if(oTest.type == 'tx'){
                        result = 'success';
                        clog("Tx: " + res);
                        //var txReceipt = web3.eth.getTransactionReceipt(res);
                        //clog(txReceipt);
                        //clog(web3.eth.getTransaction(res));
                    }else{
                        result = res;
                        clog("Call result: " + res);
                    }
                })
                .catch(function(e){
                    // search 'invalid JUMP'
                    if(e.toString().indexOf('invalid JUMP') >= 0){
                        result = 'fail';
                    }else{
                        result = e.toString();
                    }
                })
                .then(function(){
                    assert.equal(result, curTest.result, '');
                });
            });

        });
    });

    var aEventsData = [],
        aTxData = [],
        aChainyJson = [
        {
            'json': '{"id":"CHAINY", version: 1, type: "L", url: "http:\/\/site.com\/file.zip", hash: "24356450ab5de1cf7b07a85cda0ff91c0b44a347b731402c4fa6729ec7c98", filetype: "arc", filesize: 1024, description: "reports.everex.one"}',
            'result': 'success',
            'watch': true
        },
        {
            'json': '{version: 1, type: "H", hash: "ea18dbtryrtyet1eb7122c75435133b9f5002c8a19aec7e787287dc1bec83","id":"CHAINY"}',
            'result': 'fail'
        },
        {
            'json': '{version: 1, type: "T", "id":"CHAINY", description: "some text here"}',
            'result': 'fail'
        },
        {
            'json': '{"id": "CHAINY",version: 1, type: "L", url: "http://site.com/file.zip", hash: "76wert456546t7a85cda0ff91c0b44a347b731402c4fa6729ec7c98", filetype: "arc", filesize: 1024, description: "reports.everex.one"}',
            'result': 'fail'
        },
        {
            'json': '',
            'result': 'fail'
        },
        {
            'json': '{"id":"CHAINY"}',
            'result': 'fail'
        },
    ];

    var chainyCode = 'zzz';

    aChainyJson.forEach(function(aData){
        it("/Add Chainy data '" + aData.json + '\' should ' + aData.result + '/', function(done){
            var chainy = Chainy.deployed();

            setTimeout(done, 2000);

            if(aData.watch){
                var events = chainy.chainyShortLink();
                events.watch(function(error, result){
                    if(error == null){
                        aEventsData.push(result.args);
                        events.stopWatching();
                        clog(JSON.stringify(aEventsData));
                    }else{
                        clog(error);
                    }
                });
            }

            chainy.addChainyData(aData.json, {from: accounts[3]})
            .then(function(res){
                result = 'success';
                clog("Tx: " + res);
                var txReceipt = web3.eth.getTransactionReceipt(res);
                //clog(txReceipt);
                aTxData.push(txReceipt);
            })
            .catch(function(e){
                if(e.toString().indexOf('invalid JUMP') >= 0){
                    result = 'fail';
                }else{
                    result = e.toString();
                }
            })
            .then(function(){
                assert.equal(result, aData.result, '');
            });
        });
    });

    it("/Should check Chainy short link/", function(){
        var chainyLink = aEventsData[0].code.slice(0, -2),
            correctLink = 'https://new-txn-site.io/' + int2base58(parseInt(aTxData[0].blockNumber));

        assert.equal(chainyLink, correctLink, '');
    });

    it("/Should check Chainy data timestamp/", function(){
        var chainy = Chainy.deployed();
        var chainyTS = 1;

        if(aEventsData.length){
            chainyTS = aEventsData[0].timestamp;

            var aCode = aEventsData[0].code.split('/');
            if(aCode.length){
                chainyCode = aCode[aCode.length - 1];
            }
        }
        return chainy.getChainyTimestamp.call(chainyCode, {from: accounts[2]})
        .then(function(res){
            assert.equal(res.valueOf(), chainyTS, '');
        });
    });

    it("/Should check Chainy data json/", function(){
        var chainy = Chainy.deployed();

        return chainy.getChainyData.call(chainyCode, {from: accounts[2]})
        .then(function(res){
            assert.equal(res.valueOf(), aChainyJson[0].json, '');
        });
    });

    it("/Should check Chainy data sender/", function(){
        var chainy = Chainy.deployed();

        return chainy.getChainySender.call(chainyCode, {from: accounts[2]})
        .then(function(res){
            assert.equal(res.valueOf(), accounts[3], '');
        });
    });

    it("/Should check final balance of receiver address/", function(){
        clog(web3.eth.getBalance(accounts[4]));
        assert.equal(web3.eth.getBalance(accounts[4]), 100010000000000000002, '');
    });

    it("/Should check final balance of owner (accounts[5])/", function(){
        clog(web3.eth.getBalance(accounts[5]));
        // 99997326000000000000 = 100000000000000000000 - 2852200000000000 - 2821800000000000 + 3000000000000000 (releaseFunds)
        assert.equal(web3.eth.getBalance(accounts[5]), 99997326000000000000, '');
    });

});
