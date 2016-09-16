
/**
 * Copyright 2016 Everex https://everex.io
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Sample configuration file for Chainy service.
 *
 * Copy this file to config.service.js before making any changes.
 */

chainyConfig = {
    server: {
        address: 'localhost',
        port: 8344
    },
    ethereum: {
        url: 'http://localhost:8545',
        testnet: false
    },
    // Contract address
    contract: '0x0000000000000000000000000000000000000000',
    // Contract ABI
    ABI: [
        {constant:true,inputs:[{name:"code",type:"string"}],name:"getChainyData",outputs:[{name:"",type:"string"}],type:"function"},
        {constant:true,inputs:[{name:"code",type:"string"}],name:"getChainyTimestamp",outputs:[{name:"",type:"uint256"}],type:"function"}
    ],
    // Gas limit (4.5M is near block limit)
    gasLimit: 4500000,
    // Command code
    cmd: '0xac3e7d24',
    // Log topic
    topic: "0xdad5c3eecfdb62dd69e6e72053b88029e1d6277d4bc773c00fef243982adcb7d",    
    // Autopublish sender
    /*
    sender: {
        address: '0x0000000000000000000000000000000000000000',
        pk: 'private key'
    }
    */
};