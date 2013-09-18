<html>
<head>
    <title>Push notification</title>
    <meta charset="utf-8">
    <style type="text/css">
        #xxx {
            width: 80%;
            border-collapse: collapse;
        }

        #xxx .label {
            text-align: right;
        }

        #xxx td {
            padding: .3em;
        }

        input[type='text'] {
            width: 90%;
        }

        textarea {
            width: 90%;
        }

        .h_row {
            display: none;
        }

    </style>
</head>
<body>

<h1>Push more shit tool</h1>

<table border="1" id="xxx">
    <tr>
        <td class="label">Ứng dụng</td>
        <td>
            <select name="" id="slb-app-name">
                <option value="tinmoi">Tin mới</option>
                <option value="nguoiduatin">Người đưa tin</option>
                <option value="techz">Techz</option>
                <option value="thethao247">Thể thao 247</option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label">Thiết bị</td>
        <td>
            <select name="" id="slb-device"></select>
        </td>
    </tr>
    <tr>
        <td class="label">Kiểu</td>
        <td>
            <div id="msg-type">
                <input checked type="radio" value="message" name="type[]"/> Thông báo
                <input type="radio" value="news" name="type[]"/> Tin tức
                <input type="radio" value="update" name="type[]"/> Cập nhật
            </div>
        </td>
    </tr>
    <tr>
        <td class="label">Thông điệp</td>
        <td>
            <textarea id="txtMessage" cols="30" rows="10"></textarea>
        </td>
    </tr>
    <tr>
        <td class="label">NewID</td>
        <td>
            <input type="text" value="" id="txtNewID"/>
        </td>
    </tr>
    <tr class="h_row" id="branch_row">
        <td class="label">Nhánh</td>
        <td>
            <select name="" id="sbl-branch"></select>
        </td>
    </tr>

    <tr>
        <td class="label">Giới hạn</td>
        <td>
            <select id="scope">
                <option value="all">Tất cả các máy</option>
                <option value="dev">Chỉ cho máy dev</option>
                <option value="single">Chỉ cho một máy</option>
            </select>
        </td>
    </tr>
    <tr class="h_row" id="token_row">
        <td class="label">Token</td>
        <td>
            <input type="text" id="txtToken"/>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input type="button" value="Send" id="btnSend"/>
        </td>
    </tr>
</table>
<script type="text/javascript" src="<?php echo site_url('assets/js/jquery-1.9.1.js') ?>"></script>
<script type="text/javascript">
    var url = $('#txtUrl'), base_link = 'http://services.meme.vn/apps/', fn_select_app = function (appname) {
            var apps = [], branches = [];
            if (appname == 'tinmoi') {
                apps = ['android', 'iphone'];
                branches = ['tinmoi', 'giaitri', 'thethao', 'hoidap'];
            } else if (appname == 'nguoiduatin') {
                apps = ['android', 'iphone'];
            } else if (appname == 'thethao247') {
                apps = ['iphone'];
            } else if (appname == 'techz') {
                apps = ['iphone']
            }

            $('#slb-device').children().remove();
            for (var i in apps) {
                $('#slb-device').append('<option value="' + apps[i] + '">' + apps[i] + '</option>');
            }

            $('#sbl-branch').children().remove();
            if(branches.length > 0) {
                for (var i in branches) {
                    $('#sbl-branch').append('<option value="' + branches[i] + '">' + branches[i] + '</option>');
                }
                $('#branch_row').removeClass('h_row');
            } else {
                $('#branch_row').addClass('h_row');
            }
        };

    $(document).ready(function () {
        $('#slb-app-name').change(function () {
            fn_select_app($(this).val());
        });
        fn_select_app('tinmoi');
        $('#btnSend').click(function() {
            var newsid = $('#txtNewID').val();
            var scope = $('#scope').val();
            var token = $('#txtToken').val();
            var branch = $('#sbl-branch').val();
            var url = base_link + $('#slb-app-name').val() + '/push/' + $('#slb-device').val() + '?type=' + $('#msg-type :radio:checked').val()+'&message='+$('#txtMessage').val();
            if(newsid != '') {
                url += '&nid='+newsid;
            }
            if(scope != '') {
                url += '&scope='+scope;
            }
            if(token != '') {
                url += '&token='+token;
            }
            if(branch != '') {
                url += '&branch='+branch;
            }

            $.ajax({
                url : '<?php echo site_url('push_tool'); ?>',
                data: {
                    theUrl : url
                },
                success : function() {  }
            });
        });
        $('#scope').change(function() {
            if($(this).val() == 'single') {
                $('#token_row').removeClass('h_row');
            } else {
                $('#h_row').addClass('h_row');
            }
        });
    });
</script>
</body>
</html>