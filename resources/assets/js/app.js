
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


// window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example', require('./components/Example.vue'));

// Vue.component('modal', {
//     template: '#modal-template'
// })
//
// const app = new Vue({
//     el: '#app'
// });

import typeahead from "jquery-typeahead"



$.typeahead({
    input: '.js-typeahead-batches',
    minLength: 1,
    order: "asc",
    dynamic: true,
    delay: 500,
    display: ["name"],
    template: function (query, item) {

        // var color = "#777";
        // if (item.status === "owner") {
        //     color = "#ff1493";
        // }
// console.log(item);
        var temp_str = '<span class="row">'
            // '<span class="avatar">' +
            // '<img src="{{avatar}}">' +
            // "</span>" +
            + '<span class="username" style="padding-left: 10px">{{name}} ';

        if(item.show_inventory) {
            temp_str += '{{inventory}} {{uom}}';
        }

        temp_str += '</span>'
            + "</span>";

        temp_str += '<span class="row">';

            if(item.unit_cost) {
                temp_str += '<span class="unit_price" style="padding-left: 10px"><small>Cost: {{pre_tax_unit_cost}}</small></span>';
            }

            if(item.vendor) {
                temp_str += '<span class="vendor" style="padding-left: 20px"><small>{{vendor}}</small></span>';
            }

        temp_str += "</span>";


            return temp_str;

    },
    emptyTemplate: "no result for {{query}}",
    source: {
        batches: {
            // display: "label",
            // href: function (item) {
            //     // console.log(item);
            //         // return '/' + item.project.replace(/\s+/g, '').toLowerCase() + '/documentation/'
            //     },
            // data: [],
            ajax: function (query) {
                return {
                    type: "GET",
                    url: "/batches/search",
                    path: "data.batches",
                    data: {
                        q: "{{query}}"
                    },
                    callback: {
                        done: function (data) {

                            return data;
                        }
                    },
                    statusCode: {
                        401: function () {
                            location.reload();
                        }
                    }

                }
            }
        }
    },
    callback: {
        onClick: function (node, a, item, event) {

            node.parents('form#add-new-item').find('input.cog').val(item.cog);
            node.parents('form#add-new-item').find('input.batch_id').val(item.id);
            node.parents('form#add-new-item').find('input.sold_as_name').val(item.sold_as_name);
            node.parents('form#add-new-item').find('input.qty').val(item.inventory);
            node.parents('form#add-new-item').find('span.uom').text(item.uom);
            node.parents('form#add-new-item').find('span.pre-tax-cost').text("Pre-Tax: "+item.pre_tax_unit_cost);

            node.parents('form#add-new-item').find('input.sale_price').val((item.suggested_unit_sale_price != 0?item.suggested_unit_sale_price:""));

            node.parents('form#add-new-item').find('input.unit_cost').val((item.unit_cost?item.unit_cost:0));
            node.parents('form#add-new-item').find('span.unit_cost').text((item.unit_cost?"$"+item.unit_cost:""));

            if( ! item.has_cult_tax) {
                node.parents('.add-new-item').find('.pass_on_tax_cell').hide();
            } else {
                node.parents('.add-new-item').find('.pass_on_tax_cell').show();
            }
            // var uom = node.parents('.add-new-item').find('input.uom');
            // var qty = node.parents('.add-new-item').find('input.');
            // console.log(qty);
            // console.log(a);
            // console.log(item);

            // You can do a simple window.location of the item.href
            // alert(JSON.stringify(item));

        },
        onSendRequest: function (node, query) {
            // console.log('request is sent')
        },
        onReceiveRequest: function (node, query) {
            // console.log('request is received')
        },
        onCancel: function (node, item, event) {
            node.parents('form#add-new-item').find('input.cog').val(0);
            node.parents('form#add-new-item').find('input.batch_id').val(0);
            node.parents('form#add-new-item').find('input.sold_as_name').val("");
            node.parents('form#add-new-item').find('input.qty').val("");
            node.parents('form#add-new-item').find('span.uom').text("");
            node.parents('form#add-new-item').find('input.sale_price').val("");
            node.parents('form#add-new-item').find('input.unit_cost').val("");
            node.parents('form#add-new-item').find('span.unit_cost').text("");
            node.parents('form#add-new-item').find('span.pre-tax-cost').text("");
            node.parents('.add-new-item').find('.pass_on_tax_cell').show();
        }
    },
    debug: true
});


$(function () {

    $('.conf_action').click(function(e) {
        e.preventDefault();

        if( ! confirm('Are you sure?')) {
            return false;
        } else {
            $(this).parent().submit();
            return true;
        }
    });

    $('#sell_return_action').change(function() {
        if($('option:selected', this).text() == 'Return') {
            $('#sell_container').hide();
        } else {
            $('#sell_container').show();
        }
    });


    //PO & SO update
    $('a.qb_update').click(function (e) {
        e.preventDefault();

        let $this = this;
        let data = {"in_qb" : ($(this).data('in_qb')?0:1)};

        $.ajax({
            url: this.href,
            type: 'PUT',
            accept: 'application/json',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify(data), // access in body
            error: function() {
                alert('Error: Unable to update!');
            },
            dataType: 'json',
            success: function(so_obj) {
                $($this).data('in_qb', so_obj.in_qb);
                if(so_obj.in_qb) {
                    $($this).find('i.mdi').removeClass('text-danger').addClass('text-success');
                } else {
                    $($this).find('i.mdi').removeClass('text-success').addClass('text-danger');
                }
            },

        });

    });

});
