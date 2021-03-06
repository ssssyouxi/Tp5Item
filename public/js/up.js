//初始化图片对象
window.imglist = {};
$("input.single").each(function () {
    imglist[$(this).attr('id')] = $(this).prev().attr('src');
});
$("img.many").each(function () { 
    if (!imglist[$(this).attr('data-id')]) {
        imglist[$(this).attr('data-id')] = []
    }
    imglist[$(this).attr('data-id')].push($(this).attr('src'));
})

console.log(imglist);



//(单张的)上传图片
$("input[type='file'][class='single']").change(function (event) {
    var name = $(this).attr("id");
    var name1 = "#" + name;
    var formData = new FormData();
    formData.append("file", $(this).get(0).files[0]);
    console.log($(this));
    $.ajax({
        url: '/api/upload/index/ ',
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false, //不可缺
        processData: false, //不可缺
        success: function (data) {

            if (data.code == 1) {

                data.res = data.res.replace(/\\/g, "/");

                $(name1).prev().attr('src', data.res);
                imglist[name] = data.res;
                console.log(imglist);
            } else {
                alert(data.msg + ",错误为：" + data.res);
            }
        }
    });
});


//(总量可以是多张的)上传图片
$("input[type='file'][class='manyimg']").change(function (event) {
    var name = $(this).attr("id");
    var name1 = "#" + name;
    var formData = new FormData();
    formData.append("file", $(this).get(0).files[0]);
    $.ajax({
        url: '/api/upload/index/ ',
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false, //不可缺
        processData: false, //不可缺
        success: function (data) {

            if (data.code == 1) {
                data.res = data.res.replace(/\\/g, "/");
                $(name1).before("<img src='"+data.res+"' class='many' data-id='"+name+"' onclick='removeimg($(this))' style='width: 20%;max-width: 150px'/>")
                if (!imglist[name]) {
                    imglist[name] = [];
                }
                imglist[name].push(data.res);
                console.log(imglist);
            } else {
                alert(data.msg + ",错误为：" + data.res);
            }
        }
    });
});



//修改提交
$("button#submit").click(function () {
    if ($.trim($("input[name='title']").val()) == '') {
        layer.msg("请填写标题", {
            icon: 2,
            time: 3000
        });
        return false;
    }
    if ($(".tip").length > 0) {
        layer.msg("标题重复，请修改！", {
            icon: 2,
            time: 3000
        });
        return false;
    }
    console.log($(this).attr('d-type'));
    switch ($(this).attr('d-type')) {
        case 'addnews':
            var url = "/admin/news/addnews/";
            break;
        case 'updatenews':
            var url = "/admin/news/updatenews/";
            break;
        case 'addspec':
            var url = "/admin/spec/addspec/";
            break;
        case 'updatespec':
            var url = "/admin/spec/updatespec/";
            break;
    }


    console.log(window.imglist);
    var form = new FormData(document.getElementById("form-article-add"));

    // $("input.single").each(function () {
    //     form.append($(this).attr('id'), imglist[$(this).attr('id')]);
    // })
         var imglist1 = JSON.stringify(imglist);
         console.log(imglist1);
         form.append('imglist', imglist1);
    // if (r != "spec") {
    //     var imglist = JSON.stringify(imglist);
        
    //     form.append('imglist', imglist);
    //     var url = "/admin/news/updatenews";
    // } else {
    //     var url = "/admin/spec/updateSpec";
    // }
    $.ajax({
        url: url,
        type: "post",
        data: form,
        processData: false,
        contentType: false,
        success: function (data) {
            if (data.code) {

                layer.msg(data.msg + "，2秒后将关闭窗口", {
                    icon: 1,
                    time: 2000
                });
                // setTimeout(parent.location.reload(),5000);
                setTimeout(
                    function () {
                        // var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        // parent.layer.close(index); //再执行关闭 
                        parent.location.reload()
                    },
                    2000
                );
            } else {
                layer.msg(data.msg , {
                    icon: 2,
                    time: 2000
                });
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown, data) {
            console.log(XMLHttpRequest.status);

            console.log(XMLHttpRequest.readyState);

            console.log(textStatus);
            console.log(data);

        }
    });
})


//删除一张图片
function removeimg(obj) {
    layer.confirm('确认要删除这张图片吗？', function (index) {
        var id = obj.attr('data-id');
        var src = obj.attr("src");
        obj.remove();
        imglist[id].splice($.inArray(src,imglist[id]),1);
        console.log(imglist);
        // $(obj).remove();
        
        // imglist['imgurls'].remove(obj.attr("src"))
        layer.close(index);
        layer.msg("删除成功", {
            icon: 1,
            time: 1000
        });
    })
}



$("#title").keyup(function () { 
    var id=$("input[name='id']").val() ? $("input[name='id']").val() : null;
    $.ajax({
        type: "post",
        url: "/admin/spec/sameTitle",
        data: {
            "title": $("#title").val(),
            "id": id
        },

        success: function (res) {
            if (res.code) {
                $("p.tip").remove();
                $("#title").parent("div").append("<p style='color:red' class='tip'>" + res.msg + "</p>");
            } else {
                $("p.tip").remove();
            }
            
        }
    });
});


