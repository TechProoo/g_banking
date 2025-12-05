import "./bootstrap";
import "./libs/alpine";
const axios = require("axios");

// axios
//     .get("/user?ID=12345")
//     .then(function (response) {
//         // handle success
//         console.log(response);
//     })
//     .catch(function (error) {
//         // handle error
//         console.log(error);
//     });

let MetaApi;
let CopyFactory;
console.log("Hello");
alert("jelldl");

// Use the CDN-provided global if available in the browser to avoid bundling
// the Node-oriented `metaapi.cloud-sdk` into the client bundle.
if (typeof window !== 'undefined' && window.MetaApi) {
	MetaApi = window.MetaApi;
	CopyFactory = window.CopyFactory || window.MetaApi.CopyFactory;
}
