var Ioc = {};

Ioc.componentsDefinitionRaw = {/* %definitionsRawPlaceholder% */};

Ioc.ComponentDefinition = function (name, className, namespace, dependencies) {
    this.name = name;
    this.className = className;
    this.namespace = namespace;
    this.dependencies = dependencies || [];

    /**
     *
     * @returns {string}
     */
    this.getFullClassName = function () {
        return this.namespace + '.' + this.className;
    }
};

Ioc.Container = function () {
    /**
     * @type {{Ioc.ComponentDefinition}}
     */
    this.componentDefinitionList = {};

    /**
     *
     * @type {{}}
     */
    this.componentInstanceList = {};

    /**
     *
     */
    this.init = function () {
        for (var componentDefinition in Ioc.componentsDefinitionRaw) {
            var componentObject = new Ioc.ComponentDefinition(
                componentDefinition['name'],
                componentDefinition['class'],
                componentDefinition['namespace'],
                componentDefinition['dependencies']
            );

            this.componentDefinitionList[componentObject.name] = componentObject;
        }
    };

    /**
     *
     * @param componentName
     */
    this.getComponent = function (componentName) {
        // On vérifie que le composant demandé existe bien dans le container courant.
        if (typeof this.componentDefinitionList[componentName] !== 'undefined') {
            var currentComponentDefinition = this.componentDefinitionList[componentName];

            // Si le composant est déjà intancié, on réustilise son instance.
            if (typeof this.componentInstanceList[componentName] !== 'undefined') {
                return this.componentInstanceList[componentName];
            }
            // Si le composant n'as pas été instancé on instancie ces dépendence dans le vide.
//            else if (currentComponentDefinition.dependencies.length > 0) {
//                for (var dependency in currentComponentDefinition.dependencies) {
//                    this.getComponent(dependency);
//                }
//            }
            else {
                if (typeof window[currentComponentDefinition.getFullClassName()] === 'undefined') {
                    throw new Error('Impossible de trouver l\'objet "' + currentComponentDefinition.getFullClassName() + '" dans le contexte global.');
                }

                var dependenciesArray = [];
                for (var dependency in currentComponentDefinition.dependencies) {
                    dependenciesArray.push(this.getComponent(dependency));
                }

                var componentInstance = window[currentComponentDefinition.getFullClassName()].apply(this, dependenciesArray);

                return componentInstance;
            }
        } else {
            throw new Error('Le composant "' + componentName + '" n\'est pas définis dans la container.');
        }
    };

}

Ioc.Manager.init();