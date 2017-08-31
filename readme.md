# TCommerce

TCommerce is a base structure for an e-commerce, using the PHP framework Phalcon.
The structure is currently under construction, but already has some basic features.
It is a multi-modular structure, with 3 main modules by default. All modules are present in the
app folder, and the main modules are:
1. API: The E-Commerce's Api, it already has a few controllers like the products controller and users controller
2. Admin: This administrative panel
3. Frontend: The E-Commerce's store front

Each module contains its own configuration files, routes, Controllers, Views (except for the API module,
which doesn't have views), Exceptions and other specific stuff for that module.

All models are present in the Core module, that's because the models may be used in multiple modules, for example
in the API and Admin modules.
