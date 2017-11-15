/**
 * 数据预处理渲染逻辑
 * @copyright alone◎浅忆
 */

/**
 * 按需载入市区道路交通通行数据
 * @param {integer} 导出数量
 * @return null
 */
function loadAllRoadData(count) {
    $.ajax({
		url: './index.php?c=Api&mod=AllRoad',
		type: 'get',
        dataType: 'json',
        async: true,
        success: function (res) {
            $('#AllRoad').html('');
            if (typeof(count) != undefined) {
                var limit = count; //设置限制策略，后续启用局部输出模式
            }
            var color; //页面文字彩色输出
            $.each(res, function(key, value) {
                /**
                 * 根据道路tpi指数动态设置字体颜色
                 */
                if (value.tpi >= 8) {
                    color = 'am-text-danger';
                }
                else if (value.tpi >= 6) {
                    color = 'am-text-warning';
                }
                else if (value.tpi >= 4) {
                    color = 'am-text-secondary';
                }
                else if (value.tpi >= 2) {
                    color = 'am-text-primary';
                }
                else {
                    color = 'am-text-success';
                }
                $('#AllRoad').append('<tr class="even gradeC odd" role="row"><td class="sorting_1">' + value.name + '</td><td>' + value.tpi + '</td><td class="' + color + '">' + value.grade + '</td><td>' + value.speed + '</td></tr>');
                if (typeof(limit) != undefined) {
                    limit = limit - 1;
                    if (limit == 0) {
                        return false;
                    }
                }
                //console.log(value.name);
                //console.log(value.tpi);
                //console.log(value.grade);
                //console.log(value.speed);
            });
        },
        error: function () {
            alert('绍兴智慧交通后端接口异常，可能是上游服务器正在维护或已停止服务，请联系管理员核实。');
        }
    });
}

/**
 * 载入热门区域数据
 * @return null
 */
function loadHotAreaData() {
    $.ajax({
        url: './index.php?c=Api&mod=HotArea',
        type: 'get',
        dataType: 'json',
        async: true,
        success: function (res) {
            var data = new Array();
            $.each(res, function(key, value) {
                data[key] = value.tpi;
            });
            var result = arrAverageNum(data);
            $('#hotareatpi').html(result);
        },
        error: function () {
            alert('绍兴智慧交通后端接口异常，可能是上游服务器正在维护或已停止服务，请联系管理员核实。');
        }
    });
}

/**
 * 片区交通数据载入
 * @return null
 */
function loadCitySubAreaData() {
    $.ajax({
		url: './index.php?c=Api&mod=Area',
		type: 'get',
        dataType: 'json',
        async: true,
        success: function (res) {
            $.each(res, function(key, value) {
                if (value.name == '城区') {
                    $('#zcq').html(value.speed);
                }
                else if (value.name == '袍江片区') {
                    $('#pjxq').html(value.speed);
                }
                else if (value.name == '镜湖片区') {
                    $('#jhxq').html(value.speed);
                }
                else if (value.name == '高新片区') {
                    $('#cdgxq').html(value.speed);
                }
                else {
                    alert('平台升级中，请稍后再次访问。');
                }
            });
        },
        error: function () {
            alert('绍兴智慧交通后端接口异常，可能是上游服务器正在维护或已停止服务，请联系管理员核实。');
        }
    });
}

/**
 * 计算指定区域的平均tpi指数
 * @param {string} mod 计算模式
 * @return null
 */
function executeAverageSpeed(mod) {
    $.ajax({
		url: './index.php?c=Api&mod=AllRoad',
		type: 'get',
        dataType: 'json',
        async: true,
        success: function (res) {
            var data = new Array();
            $.each(res, function(key, value) {
                data[key] = value.tpi;
            });
            var result = arrAverageNum(data);
            $('#onelooptpi').html(result);
            if (result >= 8) {
                $('#mainStatusText').html('重度拥堵');
            }
            else if (result >= 6) {
                $('#mainStatusText').html('中度拥堵');
            }
            else if (result >= 4) {
                $('#mainStatusText').html('轻度拥堵');
            }
            else if (result >= 1.8) {
                $('#mainStatusText').html('基本畅通');
            }
            else {
                $('#mainStatusText').html('畅通');
            }
        },
        error: function () {
            alert('绍兴智慧交通后端接口异常，可能是上游服务器正在维护或已停止服务，请联系管理员核实。');
        }
    });
}

/**
 * 平均值求值模型
 * @param {array} arr 数据集合
 * @return mixed
 */
function arrAverageNum(arr){
    var sum = 0; //初始化种子值
    for (var i = 0; i < arr.length; i++) { //拼装数据
        sum += arr[i];
    };
    return ~~(sum/arr.length*100)/100; //返回求值
}
