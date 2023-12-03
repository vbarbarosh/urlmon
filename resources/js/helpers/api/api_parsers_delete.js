import http_delete from '../http/http_delete';

async function api_parsers_delete({parser})
{
    return http_delete(`/api/v1/parsers/${parser.uid}`);
}

export default api_parsers_delete;
