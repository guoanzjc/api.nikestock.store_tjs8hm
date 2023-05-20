define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'youhui/index' + location.search,
                    add_url: 'youhui/add',
                    edit_url: 'youhui/edit',
                    del_url: 'youhui/del',
                    multi_url: 'youhui/multi',
                    import_url: 'youhui/import',
                    table: 'youhui',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'category', title: __('Category'), operate: 'LIKE'},
                        {field: 'sku', title: __('Sku'), operate: 'LIKE'},
                        {field: 'huodongjia', title: __('Huodongjia'), operate: 'LIKE'},
                        {field: 'lineup', title: __('Lineup'), operate: 'LIKE'},
                        {field: 'bili', title: __('Bili'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
     
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
