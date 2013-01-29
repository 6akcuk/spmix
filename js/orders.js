var Order = {
  massChangeStatus: function() {
    var ids = $('input[type="checkbox"][name*="select"]:checked'), box;

    box = Box({
      title: 'Изменение статуса заказов',
      absolute: true,
      buttons: [{title: 'Выполнить'}]
    });
    box.show();
  }
}

try {stmgr.loaded('orders.js');}catch(e){}