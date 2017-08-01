var VenteApp = VenteApp || {};

(function($, Backbone, _) {

  VenteApp.VenteModel = Backbone.Model.extend({
    defaults: {
      encaisse: 0,
      montantTotal: 0,
      paymentMode: 'numeraire',
      renduMonnaie: 0
    },

    initialize: function() {
      this.on('change:montantTotal', this.setMontantTotal, this);
      this.on('change:encaisse', this.setEncaisse, this);
    },

    setEncaisse: function(model, options) {
      // On calcule le montant à rendre
      $.ajax({
        url: Routing.generate('calculate'),
        type: 'GET',
        data: {
          operation: 'soustraction',
          montant1: model.get('encaisse'),
          montant2: model.get('montantTotal')
        },
        success: function(response) {
          model.set('renduMonnaie', response.result);
          $('#monnaie').val(parseFloat(model.get('renduMonnaie')).toFixed(2));
        }
      });
    },

    setMontantTotal: function(model, options) {
      $('#totalFacture').val(model.get('montantTotal').toFixed(2));
      // On calcule le montant à rendre
      $.ajax({
        url: Routing.generate('calculate'),
        type: 'GET',
        data: {
          operation: 'soustraction',
          montant1: model.get('encaisse'),
          montant2: model.get('montantTotal')
        },
        success: function(response) {
          model.set('renduMonnaie', response.result);
          $('#monnaie').val(parseFloat(model.get('renduMonnaie')).toFixed(2));
        }
      });
    }
  })

}(jQuery, Backbone, _));
