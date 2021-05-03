$(document).ready(function(){
    DataView();
});


function DataView() {

    var param = {
        url : './include/ajax/_ajax.php',
        data : {
            "type" : "getView",
            "seq" : seq
        }
    };

    _ajax(param, function(res) {
        if (res.status === 200) {
            var data = res.data;

            //console.log(res.data);

            $("td:eq(0)").text(data[0].subject);
            $("td:eq(1)").text(data[0].reg_name);
            $("td:eq(2)").text(data[0].reg_date);
            $("td:eq(3)").text(data[0].contents);


        } else {
            //	alert('찾고자 하는 데이터가 없거나 오류로 인하여 일시적으로 정보를 가져오지 못했습니다.\n새로고침 또는 재접속해주세요!');
            console.log(res);
            return;
        }
    });
}