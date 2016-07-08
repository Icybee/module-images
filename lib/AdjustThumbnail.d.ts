declare namespace Icybee.Images {

    namespace AdjustThumbnail {

        interface ChangeEvent extends Icybee.Adjust.ChangeEvent {
            target: Adjust
            value: string|number|null
        }

    }

    class AdjustThumbnail extends Icybee.Adjust {
        public value: string
        protected decodeOptions(url: string): Object
        protected onChange(ev: Icybee.Nodes.AdjustNode.ChangeEvent)
    }

}
