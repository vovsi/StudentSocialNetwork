// Сформировать строку со всеми смайликами (их коды)
function getSmilesCode() {
    let res = "";
    for (let i = 1; i < 90; i++) {
        let id = '';
        if (i >= 10) {

            id = String(i).substr(0, 1);
        }
        res += "&" + id + ":" + i + " ";
    }
    return res;
}