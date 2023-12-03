import bytes from 'bytes';

function format_size(v)
{
    return bytes(v);
}

export default format_size;
