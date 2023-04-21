const logger = require('./log/Log')({});
const R       = require('ramda'),
      Vimeo   = require('@vimeo/vimeo/index').Vimeo,
      config  = require('config');

const { jsonResponse } = require('@businesswire/bw-node-lambda-common/utils/jsonResponse');

async function makeRequest(lib,vidId){
  // Make an API request
  lib.request({
    // This is the path for the videos contained within the staff picks channels
    method: 'PATCH',
    path: `/videos/${vidId}`,
    query: {
      'privacy':  {view: 'anybody'}
    }
  }, function (error, body, statusCode) {
    if (error) {
      logger.debug({ msg : 'news-process-pipeline-disclose-smm',statusCode });
      logger.debug(error );
      return error;
    } else {
      return body;
    }
  });
}

const handler = async function(event) {
    try{
        const request = R.compose(R.defaultTo({}), R.prop('detail'), JSON.parse, R.prop('body'), R.head, R.prop('Records'))(event);
        const vidId = R.path(['vidId'])(request);

        config.vimeoclient_id = "f328eccd51b6ec8c9d48da5f686d1c9543043f04";
        config.vimeoclient_secret = "WPuSzuUx+K3Lurci1rCNxY5Vy/OIGRqCbaz+tBmmcimxpVIoeoW1NPmxySX2QEUIlCuFVJIP7ouVUti3qMeOAPh96HXICkmQjRBir0yHoKPR6ZzIiciibOKatHS1GLCf";
        config.vimeoaccess_token = "e102d707ef663e91444fd78c299142eb";
        const lib = new Vimeo(config.vimeo.client_id, config.vimeo.client_secret,config.vimeo.access_token);
        if (config.vimeo.access_token) {
          lib.setAccessToken(config.vimeo.access_token);
          const result = await makeRequest(lib,vidId);
          return jsonResponse({ data : result });
        } else {
          lib.generateClientCredentials('public', async function (err, response) {
            if (err) {
              logger.debug({ msg : 'news-process-pipeline-disclose-smm',err });
              return jsonResponse({ statusCode : 400, data : { err } });
            }
              // Assign the access token to the library.
              lib.setAccessToken(response.access_token);
              const result = await makeRequest(lib,vidId);
              return jsonResponse({ data : result });
            });
          }
    }
    catch (err) {
        logger.debug({ msg : 'Error Release Disclose Smm' });
        logger.debug(err);
        return jsonResponse({ statusCode : 400, data : { err } });
      }
};


exports.handler = handler;
const testEvent = {
  Records : [{
   body : '{"detail": {"vidId":"818361708"} }'
  }]
 };
 handler(testEvent,{})