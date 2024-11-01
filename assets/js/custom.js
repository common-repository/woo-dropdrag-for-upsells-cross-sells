var _drop_drag;

(function($){

	"use strict";
   var $document = $( document );

	if ($('#linked_product_data').length > 0 && $('#drop_drap_linked_product_data').length > 0){
      $('#linked_product_data').remove();
   }


   $('#drop_drap_product_options').tabs({
      create: function(event, ui) {
         var cat_id = document.getElementById(ui.panel.attr('id'));
         set_up_category_sort(cat_id);
      },
      beforeActivate: function (event, ui) {
         var cat_id = document.getElementById(ui.newPanel.attr('id'));
         set_up_category_sort(cat_id);
      }
   });

   function set_up_category_sort(id){
      Sortable.create(id, {
         sort: false,
         group: {name:'up-cross-sells', pull: 'clone',put: false},
         animation: 150,
         onFilter: function (evt) {
            evt.item.parentNode.removeChild(evt.item);
         },
         onUpdate: function (/**Event*/evt){
            var item = evt.item; // a link to an element that was moved
            console.log(evt.item)
         },
         onEnd: function (/**Event*/evt) {
            evt.oldIndex;  // element's old index within parent
            evt.newIndex;  // element's new index within parent
            // console.log('to',evt.to )
            // console.log('from',evt.from )
            // console.log('new_parent',evt.newIndex )
            // console.log('old_parent',evt.oldIndex )
         },
         store: {
            /**
             * Get the order of elements. Called once during initialization.
             * @param   {Sortable}  sortable
             * @returns {Array}
             */
            get: function (sortable) {
               var order = localStorage.getItem(sortable.options.group.name);
               return order ? order.split('|') : [];
            },

            /**
             * Save the order of elements. Called onEnd (when the item is dropped).
             * @param {Sortable}  sortable
             */
            set: function (sortable) {
               var order = sortable.toArray();
               localStorage.setItem(sortable.options.group.name, order.join('|'));
            }
         },
         // onSort: function (/**Event*/evt) {
         //    console.log('onSort.cat', evt.item)
         // },

      });
   }
   

   var up_sells_id = document.getElementById('upsells-area');
   var up_sells = Sortable.create(up_sells_id, {
      group: 'up-cross-sells',
      animation: 150,
      filter: '.js-remove',
      onFilter: function (evt) {
         evt.item.parentNode.removeChild(evt.item);
      },
      onSort: function (/**Event*/evt) {
         var listItems = $("#upsells-area li");
         var orders = [];
         listItems.each( function(index, el){
            var data_id = $(el).data('id');
            if($.inArray(data_id, orders) == -1){
               var input = $(el).find('input').remove();
               el.insertAdjacentHTML('beforeend','<input type="hidden" name="upsell_ids[]" value="'+data_id+'">')
               orders.push(data_id);
            }else{
               $(el).remove();
            }
            
         });
      }

   });

   function hasClassJS(element, cls) {
       return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
   }

   // var order = up_sells.toArray();
   // console.log(order)
   var cross_sells = document.getElementById('cross-sells-area');
   Sortable.create(cross_sells, {
      group: 'up-cross-sells',
      animation: 150,
      filter: '.js-remove',
      onFilter: function (evt) {
         evt.item.parentNode.removeChild(evt.item);
      },
      onSort: function (/**Event*/evt) {
         var listItems = $("#cross-sells-area li");
         var orders = [];
         listItems.each( function(index, el){
            var data_id = $(el).data('id');
            if($.inArray(data_id, orders) == -1){
               var input = $(el).find('input').remove();
               el.insertAdjacentHTML('beforeend','<input type="hidden" name="crosssell_ids[]" value="'+data_id+'">')
               orders.push(data_id);
            }else{
               $(el).remove();
            }
            
         });
      }
   });

   var groupeds = document.getElementById('grouped-products-area');
   Sortable.create(groupeds, {
      group: 'up-cross-sells',
      animation: 150,
      filter: '.js-remove',
      onFilter: function (evt) {
         evt.item.parentNode.removeChild(evt.item);
      },
      onSort: function (/**Event*/evt) {
         var listItems = $("#cross-sells-area li");
         var orders = [];
         listItems.each( function(index, el){
            var data_id = $(el).data('id');
            if($.inArray(data_id, orders) == -1){
               var input = $(el).find('input').remove();
               el.insertAdjacentHTML('beforeend','<input type="hidden" name="crosssell_ids[]" value="'+data_id+'">')
               orders.push(data_id);
            }else{
               $(el).remove();
            }
            
         });
      }
   });
   
})(jQuery);

