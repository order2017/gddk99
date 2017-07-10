// Generated by CoffeeScript 1.6.3
var domConverter;

require('./_prepare');

domConverter = mod('domConverter');

describe("input types");

it("should work with objects", function() {
  return domConverter.objectToDom({});
});

it("should work with arrays", function() {
  return domConverter.objectToDom([]);
});

it("should not work with other types", function() {
  return (function() {
    return domConverter.objectToDom('a');
  }).should["throw"](Error);
});
