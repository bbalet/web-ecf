import { Controller } from '@hotwired/stimulus'
import Masonry from 'masonry-layout'
import imagesLoaded from 'imagesloaded'

/*
 * This controller is used to display the last movies on the homepage with a masonry layout
 * 
 */
export default class extends Controller {


    /**
     * This method is used to setup masonry layout for the homepage
     * Because we use Hotwire Turbo, the document's onload event is not triggered when navigating back to the movies page.
     * So we need to use the Turbo's lifecycle events to initialize the masonry layout.
    */
    connect() {
        imagesLoaded('#moviesDeck', () => {
            const msnry = new Masonry('#moviesDeck', {
                percentPosition: true
            })
        })
    }
}
