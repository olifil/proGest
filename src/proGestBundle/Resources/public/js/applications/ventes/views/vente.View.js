var VenteApp = VenteApp || {};

(function($, Backbone, _) {

  VenteApp.VenteView = Backbone.View.extend({

    el: "#vente-content",

    initialize: function() {
      productsList.on('add', this.displayProduct, this);
      productsList.on('remove', this.removeProduct, this);
      productsList.fetch();
    },

    events: {
      "click #btnProductAdd": "addProduct",
      "click #btnVenteSave": "saveVente",
      "click #btnVenteUndo": "undoVente",
      "change #quantite": "setQuantite",
      "change #encaisse": "setEncaisse",
      "change #paymentMode": "setPaymentMode",
    },

    addProduct: function() {
      // On ajoute le produit à la collection
      productsList.create(product);
      // On vide le formulaire de recherche de produit
      $('#insertForm').attr('data', '');
      $('#reference').val('').attr('placeholder', 'Saisissez la reférence');
      $('#nom').val('');
      $('#stock').val('');
      $('#quantite')
        .attr('max', 0)
        .val(1)
        .attr('disabled', '');
      $('#prixAchat').val('');
      $('#montantTotal').val('');
      $('#btnProductAdd').attr('disabled', '');
      // Activation du bouton de sauvegarde
      $('#btnVenteSave').removeAttr('disabled');
    },

    displayProduct: function(product) {
      // On Ajoute la vue du produit
      view = new VenteApp.ProductView({model: product});
      $('#detail').append(view.render().el);
      // On recalcule le montant total de la vente
      $.ajax({
        url: Routing.generate('calculate'),
        type: 'GET',
        data: {
          operation: 'addition',
          montant1: parseFloat($('#totalFacture').val()),
          montant2: product.get('montantTotal')
        },
        success: function(response) {
          vente.set('montantTotal', response.result);
        }
      });
    },

    removeProduct: function(product) {
      if (productsList.length == 0 ) {
        $('#btnVenteSave').attr('disabled', '');
      }
      // On recalcule le montant total de la vente
      $.ajax({
        url: Routing.generate('calculate'),
        type: 'GET',
        data: {
          operation: 'soustraction',
          montant1: parseFloat($('#totalFacture').val()),
          montant2: product.get('montantTotal')
        },
        success: function(response) {
          vente.set('montantTotal', response.result);
        }
      });
    },

    setEncaisse: function(event) {
      vente.set({
        encaisse: parseFloat(event.currentTarget.value).toFixed(2)
      });
      $('#encaisse').val(vente.get('encaisse'));
    },

    setPaymentMode: function(event) {
      var paymentMode = event.currentTarget.value;
      vente.set({
        paymentMode: paymentMode
      })
      switch (paymentMode) {
        case "numeraire":
          $('#encaisse, #monnaie')
            .val('0.00');
          $('#encaisse')
            .removeAttr('disabled');
          break;
        default:
          $('#encaisse, #monnaie')
            .val('0.00')
            .attr('disabled', '');
      }
    },

    setQuantite: function(event) {
      var quantite = parseInt(event.currentTarget.value);
      product.set({
        quantite: quantite
      });
    },

    saveVente: function(event) {
      var _this = this;
      $.ajax({
        url: Routing.generate('vente.add'),
        type: "POST",
        data: {
          vente: vente.toJSON(),
          products: productsList.toJSON()
        },
        beforeSend: function() {
          // Mise en place du loader
          icon = $('#btnVenteSave-icon').attr('class');
          $('#btnVenteSave-icon')
           .removeClass('icon')
           .addClass('fa fa-spinner fa-pulse');
          console.log(icon);
        },
        success: function(response) {
          // Supprimer les valeurs du localStorage
          // Retrait du loader
          $('#btnVenteSave-icon')
           .removeClass('fa fa-spinner fa-pulse')
           .addClass(icon);
          _this.removeBddItems();
          window.location = Routing.generate('vente_show', {'id': response.vente.id });
        }
      });
    },

    undoVente: function(event) {
      this.removeBddItems();
      window.location = Routing.generate('ventes');
    },

    removeBddItems: function() {
      var bdName = 'venteDB' + $('#now').attr('data');
      $.each(productsList.toJSON(), function(index, value){
          localStorage.removeItem(bdName+'-'+value.id);
      });
      localStorage.removeItem(bdName);
    }

  })

}(jQuery, Backbone, _));
