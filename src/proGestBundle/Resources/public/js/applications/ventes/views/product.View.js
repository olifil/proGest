var VenteApp = VenteApp || {};

(function($, Backbone, _) {

  VenteApp.ProductView = Backbone.View.extend({

    tagName: 'tr',
    template: _.template($('#view_product').html()),

    initialize: function() {

    },

    events: {
      "click #editProduct": "editProduct",
      "click #removeProduct": "removeProduct",
      "change #singleProduct-quantite": "setQuantite"
    },

    editProduct: function(event) {
      this.$('#singleProduct-quantite').removeAttr('disabled');
    },

    removeProduct: function() {
      this.model.destroy();
      this.remove();
    },

    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        return this;
    },

    setQuantite: function(event) {
      var quantite = parseInt(this.$('#singleProduct-quantite').val());
      this.model.set({
        quantite: quantite
      });
    }

  })

}(jQuery, Backbone, _));
