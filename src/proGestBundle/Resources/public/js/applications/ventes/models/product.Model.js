var VenteApp = VenteApp || {};

(function($, Backbone, _) {

  VenteApp.ProductModel = Backbone.Model.extend({

    localStorage: new Backbone.LocalStorage('venteDB' + $('#now').attr('data')),

    initialize: function() {
      this.on('change:prixUnitaire', this.setPrixUnitaire, this);
      this.on('change:montantTotal', this.setMontantTotal, this);
      this.on("change:quantite", this.setQuantite, this);
      this.on('change:stock', this.setStock, this);
    },

    render: function() {
      $('#insertForm').attr('data', this.get('id'));
      $('#nom').val(this.get('nom'));
      $('#stock').val(this.get('stock'));
      $('#quantite')
        .attr('max', this.get('stock') + 1)
        .val(1)
        .removeAttr('disabled');
      $('#prixAchat').val(this.get('prixUnitaire').toFixed(2));
      $('#montantTotal').val(this.get('montantTotal').toFixed(2));
      $('#btnProductAdd').removeAttr('disabled');
    },

    setMontantTotal: function(model, options) {
      this.set({
        montantTotal: parseFloat(this.get('montantTotal')).toFixed(2)
      });
      $('#montantTotal')
        .removeAttr('disabled')
        .val(this.get('montantTotal'))
        .attr('disabled', 'disabled');
    },

    setPrixUnitaire: function(model, options) {
      this.set({
        prixUnitaire: parseFloat(this.get('prixUnitaire')).toFixed(2)
      });
      $('#prixAchat').val(model.get('prixUnitaire'));
    },

    setQuantite: function(model, options) {
      // Quel est le prix à prendre en compte ?
      if ( this.get('prixMultiple').length > 0 ) {
        // Il existe des prix Multiples
        var prixMultiple = this.get('prixMultiple');
        $.each(prixMultiple, function(index, value) {
          // On teste si la quantité est inférieure à la tranche en cours
          if (model.get('quantite') < value.quantite) {
            if (index == 0 ) {
              model.set({
                prixUnitaire: model.get('prixAchat')
              });
              return false;
            } else {
              // La quantité est inférieure à l'une des tranches
              // La quantité ne correspond pas au prix de base
              model.set({
                prixUnitaire: prixMultiple[index - 1].prix
              });
              return false;
            }
          } else {
            // La quantité est supérieure à toute les tranches
            model.set({
              prixUnitaire: prixMultiple[index].prix
            });
          }
        })
      } else {
        // Le produit n'a qu'un seul prix
        model.set({
          prixUnitaire: model.get('prixAchat')
        });
      }

      // Faire calculer à php le montant total
      $.ajax({
        url: Routing.generate('calculate'),
        type: 'GET',
        data: {
          operation: 'multiplication',
          montant1: model.get('quantite'),
          montant2: model.get('prixUnitaire')
        },
        success: function(response) {
          model.set({
            montantTotal: response.result
          });
        }
      });

      // On recalcule les données
      this.set({
        stock: this.get('stockInitial') - this.get('quantite')
      });
    },

    setStock: function(model, options) {
      $('#stock')
        .removeAttr('disabled')
        .val(this.get('stock'))
        .attr('disabled', 'disabled');
    }
  })

}(jQuery, Backbone, _));
