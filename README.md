# translAPI
Toolkit for creating YAML (initially) map based interfaces to integrate with API services.

Features are intended to include:
 * Create required complex array structure from simpler input arrays.
 * Perform optional validation and filter transformations on the input, including value defaulting.
 * Convert responses to simpler and more consistent (i.e. fix SOAP's handling of multiple values where single entries turn up as a scalar) format.
 * Optional validation and filtering of respones.
 * Generate form structures from the input map schema to gather required information, including HTML5 form validation.
 * Automatically generate help documentation from both the request and response map schemas.
 * Named after my bike, the ever lovely XL 600VK Transalp.

> Currently being developed as a [Royal Mail Shipping API specific version](https://github.com/turtledesign/royalmail-php) - this module will be an extraction of a general case version from that.

