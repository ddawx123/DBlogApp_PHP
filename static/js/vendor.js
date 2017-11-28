/**
 * Global Function
 * @description 公共函数
 * @author David Ding
 * @copyright 2012-2017 DingStudio All Rights Reserved
 */

/**
 * 获取Url参数
 * @param string key url参数名
 */
function getUrlParam(key) {
    var search = window.location.search;
    if (!search) {
        return '';
    }
    var reg = new RegExp('.*' + key + '=([^&]*)' + '.*');
    return decodeURIComponent(search.replace(reg, '$1') || '');
}

/**
 * 异步登录过程
 */
function postLogin() {
    var user = document.getElementById('username').value;
    var pswd = document.getElementById('password').value;
    if (user == '' || pswd == '') {
        alert('用户名或密码不能为空，请重试。');
        return false;
    }
    var json = '{"uname":"' + user + '","upswd":"' + MD5(pswd) + '"}';
    //console.log(json);
    var xhr;
    if (window.XMLHttpRequest) {
      xhr = new XMLHttpRequest();
    }
    else {
      xhr = new ActiveXObject('Microsoft.XMLHTTP');
    }
    xhr.onreadystatechange = function() {
      if (xhr.readyState == 4) {
        if (xhr.status == 200) {
          var data = eval("(" + xhr.responseText + ")");
          if (data.code == 0) {
            console.log(data.message);
            alert('登录成功！即将为您刷新页面。');
            if (getUrlParam('callback') == '') {
                location.href = './index.php?c=index';
            }
            else {
                location.href = decodeURI(getUrlParam('callback'));
            }
          }
          else if (data.code == -8) {
            console.log(data.message);
            alert('抱歉，服务器忙碌。暂时无法处理您的登录请求，请稍后再次尝试。');
            return false;
          }
          else if (data.code == -1) {
            console.log(data.message);
            alert('用户名或密码不正确，请重试。');
            return false;
          }
          else if (data.code == -3 || data.code == -5) {
            console.log(data.message);
            alert('服务器不可接受的数据类型，可能是由于您的浏览器编码兼容问题所致！建议更换最新的浏览器再次尝试。');
            return false;
          }
          else {
            console.log(data.message);
            alert('数据写入失败，可能是数据中心忙碌或您的数据库空间处于只读状态！请核查这些问题后再次尝试。');
            return false;
          }
        }
        else if (xhr.status == 500) {
          alert('抱歉，服务器忙碌或配置存在异常！如果该现象多次出现，请联系站点管理员。');
          return false;
        }
        else {
          alert('发生未知错误，错误代码：' + xhr.status + '。请将此错误代码提供给软件开发者作进一步分析！');
          return false;
        }
      }
    }
    xhr.withCredentials = true;
    xhr.open('POST', './api.php?action=auth/login', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('data=' + json);
}