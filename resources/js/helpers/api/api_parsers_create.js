import http_post_json from '../http/http_post_json';

async function api_parsers_create({parser})
{
    const {label, engine, match,  config} = parser;
    return http_post_json('/api/v1/parsers', {parser: {label, engine, match, config}});
}

export default api_parsers_create;
