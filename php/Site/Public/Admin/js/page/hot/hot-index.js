$(function(){
    $('#js_submit').on('click',function(){
        ajax({
            url:'/Admin/Hot/HotDetail',
            data:{
                IndexPurchase:$('#js_index').textbox('getValue')
            }
        }).then(function(rs){
            $.messager.alert('提示',rs.msg);
        },function(rs){
            //$.messager.alert('提示',rs.msg);
        })
    })
});