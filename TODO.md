# TODO

This file contains ideas, todos and upcoming features for Raxos. Nothing is set in stone, and things may change.

## Router

- Check and validate that multiple request models can be used within a route.
- Rethink the internal routing structure to allow for improved caching.

## OpenAPI

- When mutiple request models are allowed in Router, fetch them from the parameters and build a structure from that. The `requestModel` property should then be removed from the Endpoint attribute.
