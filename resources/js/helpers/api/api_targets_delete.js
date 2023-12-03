import http_delete from '../http/http_delete';

async function api_targets_delete({target})
{
    return http_delete(`/api/v1/targets/${target.uid}`);
}

export default api_targets_delete;
