function format_date(d)
{
    d = d instanceof Date ? d : new Date(d);
    return `${d.getFullYear()}/0${d.getMonth() + 1}/0${d.getDate()} 0${d.getHours()}:0${d.getMinutes()}`.replace(/\b0+(\d\d)/g, '$1');
}

export default format_date;
