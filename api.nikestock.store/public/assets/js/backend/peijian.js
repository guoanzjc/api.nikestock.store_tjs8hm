define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'peijian/index' + location.search,
                    add_url: 'peijian/add',
                    edit_url: 'peijian/edit',
                    del_url: 'peijian/del',
                    multi_url: 'peijian/multi',
                    import_url: 'peijian/import',
                    table: 'peijian',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'date', title: __('Date'), operate: 'LIKE'},
                        {field: 'store', title: __('Store'), operate: 'LIKE'},
                        {field: 'sku', title: __('Sku'), operate: 'LIKE'},
                        {field: 'lianjie', title: __('Lianjie'), operate: 'LIKE'},
                        {field: 'size', title: __('Size'), operate: 'LIKE'},
                        {field: 'category', title: __('Category'), operate: 'LIKE'},
                        {field: 'des', title: __('Des'), operate: 'LIKE'},
                        {field: 'yuanjia', title: __('Yuanjia'), operate: 'LIKE'},
                        {field: 'xianjia', title: __('Xianjia'), operate: 'LIKE'},
                        {field: 'shuliang', title: __('Shuliang'), operate: 'LIKE'},
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
