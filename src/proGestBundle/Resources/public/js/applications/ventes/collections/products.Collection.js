var VenteApp = VenteApp || {};

(function($, Backbone, _) {

    VenteApp.ProductsCollection = Backbone.Collection.extend({

      model: VenteApp.ProductModel,

      localStorage: new Backbone.LocalStorage('venteDB' + $('#now').attr('data')),

    });

}(jQuery, Backbone, _));
