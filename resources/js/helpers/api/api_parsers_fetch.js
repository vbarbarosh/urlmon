import http_get_json from '../http/http_get_json';

async function api_parsers_fetch({parser_uid})
{
    return http_get_json(`/api/v1/parsers/${parser_uid}`);
}

export default api_parsers_fetch;
