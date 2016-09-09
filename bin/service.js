/**
 * 
 npm install -g es6-shim ethereumjs-tx web3 request json-rpc2 
 */
require('es6-shim');

var fs = require('fs');
var Tx = require('ethereumjs-tx');
var rq = require('request');
var url = require('url');

var cfgFile = __dirname + '/../cfg/config.service.js';
if(fs.existsSync(cfgFile)){
    require(cfgFile);
}

if('undefined' === typeof(chainyConfig)){
    throw 'Configuration file not found';
}

String.prototype.padLeft = function(len, c){
    var s = this, c = c || '0';
    while(s.length < len) s = c+ s;
    return s;
}

String.prototype.crop0x = function(){
    return this.toLowerCase().replace(/^0x/, '');
}

var rpc = require('json-rpc2');
var Web3 = require('web3');
var web3 = new Web3(new Web3.providers.HttpProvider(esConfig.ethereum.url));
var server = rpc.Server.$create();

server.on('error', function(err){
    console.log(err);
});

Chainy = {
    // Creates transaction (and sends it if specified address is system
    add: function(args, opt, callback){
        var from = args[0], data = args[1];
        var systemSender = chainyConfig.sender ? chainyConfig.sender.address.toLowerCase() : false;
        var servicePK = chainyConfig.sender.pk;
        var isSystem = (from === systemSender);
        var result = {};
        try {
            var rawTx = {
                from: from,
                to: chainyConfig.contract,
                nonce: Chainy._getNonce(from),
                gasPrice: '0x' + web3.eth.gasPrice.toString(16),
                gasLimit: chainyConfig.gasLimit,
                value: isSystem ? '0x00' : ('0x' + (3000000).toString(16)),
                data: chainyConfig.cmd + "20".padLeft(64) + data.length.toString(16).padLeft(64) + new Buffer(data).toString("hex")
            };
            var eTx = new Tx(rawTx);
            result = eTx.serialize().toString('hex');
        }catch(e){
            callback('Create TX failed', null);
            return;
        }
        if(isSystem){
            // Sign
            var privateKey = new Buffer(servicePK.crop0x(), 'hex');
            var eTx = new Tx(new Buffer(result.crop0x(), 'hex'));
            eTx.sign(privateKey);
            var result = eTx.serialize().toString('hex');
            // Send
            try{
                web3.eth.sendRawTransaction('0x' + result.crop0x(), callback);
            }catch(e){
                callback('Send failed', null);
                return;                
            }
            return;
        }

        callback(null, result);
    },
    // Returns chaint data by code
    get: function(args, opt, callback){
        var code = args[0];
        var result = false;
        try {
            var contract = web3.eth.contract(chainyConfig.ABI).at(chainyConfig.contract);
            result = {
                timestamp:  contract.getChainyTimestamp(code),
                data:       contract.getChainyData(code)
            }
        }catch(e){}
        callback(null, result);
    },
    getLink: function(args, opt, callback){
        var txHash = args[0];
        var result = {};
        try{
            return web3.eth.getTransactionReceipt('0x' + txHash.crop0x(), function(cb){
                return function(error, receipt){
                    var link = '';
                    if(!error && receipt && receipt.logs && receipt.logs.length){
                        var log = receipt.logs[0];
                        if(chainyConfig.topic === log.topics[0]){
                            try {
                                var data = log.data.slice(192).replace(/0+$/, '');
                                link = new Buffer(data, 'hex').toString();
                            }catch(e){}
                        }
                    }
                    cb(null, link);
                }
            }(callback));
        }catch(e){}
        callback(null, result);
    },
    // Get current nonce for address
    _getNonce: function(address){
        var nonce = 0;
        try {
            nonce = parseInt(web3.eth.getTransactionCount('0x' + address.crop0x()));
        }catch(e){}
        return nonce;
    },
}

server.expose('add', Chainy.add);
server.expose('get', Chainy.get);
server.expose('getLink', Chainy.getLink);
var httpServer = server.listen(chainyConfig.server.port, chainyConfig.server.address);
