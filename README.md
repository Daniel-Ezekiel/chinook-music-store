<h1 style="text-align: center">CHINOOK MUSIC STORE - USER MANUAL</h1>

## Table of Contents

- [Overview](#overview)
- [Tools and Technologies](#tools-and-technologies)
- [Features](#features)
  - [View Albums](#view-albums)
  - [Album pagination](#album-pagination)
  - [Sorting the list of Albums](#sorting-the-list-of-albums)
  - [Searching the Database](#searching-the-database)
  - [View Single Album Details](#view-single-album-details)
  - [Delete an Album and its Tracks](#delete-an-album-and-its-tracks)
  - [Inserting a New Album](#inserting-a-new-album)
  - [Update existing Album](#update-existing-album)

## Overview

Welcome to the **Chinook Music Store**. This project is a **Database-driven CRUD application** built to allow the Chinook Music Store administrator or staff to manage the catalog/inventory of the store. The Chinook Database delivers a rich dataset and structure around which this project is built.

![Preview of the Chinook Music Store](img/doc-images/homepage.png)

## Tools and Technologies

This project is built using the following tools and technologies:

- **HTML5** and **CSS3** for the structure and user interface,
- **JavaScript** for interaction and behaviour,
- **PHP** for the logic, and
- **MySQL** for Database management.

## Features

As a fully functional CRUD application, the Chinook Music Store web application showcases the fundamental functionalities for any CRUD application: **Create**, **Read**, **Update** and **Delete**.

The full scope of all features and functionalites for this project are as follows:

1. View Albums
2. Album pagination
3. Sorting the list of Albums
4. Searching the Database
5. View single Album details
6. Delete an Album and its Tracks
7. Update existing Album
8. Inserting a New Album

<br>

### View Albums

When the Chinook Music Store homepage comes up, it shows the title of the web application, a button to insert a new Album, and two forms that allow the user to sort through the list of Albums or search for an Album or artist. Below the forms, a table that displays all Albums in the Chinook Music Store is also displayed.

In this table, the respective columns show the Album titles, the Artist names, and Actions (View **Details**, **Update** Album information, **Delete** Album) that the user can carry out to manage these Albums.

![Preview of the Chinook Music Store](img/doc-images/homepage.png)

<br>

### Album Pagination

The Chinook Music Store Database holds information for over 300 Albums and viewing these on a single page is not user friendly with the seemingly endless scrolling. Hence, as an additional functionality, pagination is implemented for the table results. The user can select what page to load and on clicking the 'Go to Page' button, set of results with respect to that page get loaded.

![Pagination functionality](img/doc-images/pagination.png)

On selecting page 10 and clicking the 'Go to Page' button, the result shown is as seen below

![Pagination functionality 2](img/doc-images/page-10.png)

<br>

### Sorting the list of Albums

To facilitate ease of catalog management, the information displayed using the table can be sorted in different ways. The ways implemented in the web application are as follows:

- **Default**: Sorts Albums by their ID in the Database
- **Album Title (Ascending)**: Sorts Albums table by the Album titles in ascending order
- **Album Title (Descending)**: Sorts Albums table by the Album titles in descending order
- **Artist Name (Ascending)**: Sorts table by artist names in ascending order
- **Artist Name (Descending)**: Sorts table by artist names in descending order

To implement this feature, after selecting an option from the dropdown, the user simply clicks the 'Apply' button to sort the table based on their choice.

![Sorting options](img/doc-images/sort.png)

The image below shows the table after it has been sorted using the Artist names in ascending order.

![Sorting example](img/doc-images/sort-name-ascending.png)

<br>

### Searching the Database

Another functionality implemented in the Chinook Music Store project is one that allows that user to filter through the list of Albums in the Database based on their search query. To use this feature, the user enters a value in the search field as shown in the right-hand side of the image below and selects an option, whether to filter/search by Album Title or Artist name.

![Searching by artist name](img/doc-images/search.png)

On clicking the Search button, a request is sent to search through the database to get their search results. On clicking the search button to search for Albums by Accept, here is the result

![Sorting options](img/doc-images/search-example.png)

<br>

### View Single Album details

Now for each table row, under the actions column, there is a 'Details' button which when clicked leads to the page that shows the user all the necessary information about the selected Album.

![Table row](img/doc-images/table-row.png)

On the details page, it shows the information about the Album like the Album title, Artist name and all the Tracks under that Album using a table format. The table also shows, the Track composer, Track length/duration, size, and its Unit Price.

As a means to manage albums, the user can use the 'Update Album' and 'Delete Album' buttons on the right-hand side, just above the Tracks table to update or delete an Album.

![Album details](img/doc-images/album-details.png)

From the picture above, we can see the details for the Album by AC/DC titled "For Those About To Rock We Salute You".

<br>

### Delete an Album and its Tracks

To delete an Album and its associated Tracks, the 'Delete' button is available on each table row. The 'Delete' button is also available on the details page as seen in the previous section.

On clicking the button, a pop-up modal comes up to get confirmation of the user's choice to delete an album. This is seen in the example below when the delete button is clicked for Album 349. By using this modal pop-up, the user knows which Album was selected for deletion and its associated Artist. This provides some form of tracking to know which Album is about to be deleted and confirm user choice.

![Delete modal](img/doc-images/delete-modal.png)

If the user still goes ahead to click the delete button, a final confirmation block pops up somewhat like two-factor authentication for deletion.

![Final delete confirmation](img/doc-images/final-delete-confirmation.png)

On confirming the delete option by selecting the OK button, the Album and its Tracks get deleted. The artist does not get deleted because that artist might still have another Album in the Database. At any point between the two confirmations, the user may decide to cancel and the delete process will be halted.

<br>

### Inserting a New Album

The 'Insert Album' button on the homepage is used to add a new Album to the Chinook Music Store Database.

![Insert Album button](img/doc-images/insert-album-button.png)

On clicking this button, the user is directed to a page with a form with which they can enter the details of the Album they wish to add to the Database.

The information the user gets to add includes:

- Album Title
- Artist name (the user can select from the list of artists available or enter a new artist name)
- Tracks for the Album

![Insert Album form](img/doc-images/insert-album.png)

From the image above, it is seen that the default state of the form only displays one (1) available Track field. The 'Add Track' button at the bottom section of the form allows the user to add an extra form field which can be used to add a new Track detail for the Album. If the user decides to remove any extra Track field, they can use the 'X' button next to the input field as seen below. The field for track 1 cannot be removed because an album must have at least one (1) track.

![Insert Album form](img/doc-images/insert-album2.png)

An extra feature to test how the insert functionality works is the 'Populate Form' button at the bottom section of the form. Clicking this button populates the form with default Album details that includes an Album Title called 'Work of Art' by 'Asake' which includes fourteen (14) Tracks.

![Insert Album form](img/doc-images/insert-album3.png)

After the user may have entered all the necessary details for the Album they want to add, they can go ahead to click the Insert Album button to submit the form and add the Album as a new entry into the Database. This then redirects the user to the details page for the album they just added.

![Details page after inserting album](img/doc-images/insert-album4.png)

<br>

### Update existing Album

Now, when a user wishes to change or update the details of an Album, the update button in the actions column on the table of Albums can be used to do so. On the 'Details' page for albums, there is an 'Update' button to do this as well. On clicking this button, the user is directed to a page where they can update the details of an Album. The details for the selected album to be updated gets populated into the form as the page loads.

These details include:

- Album title
- Artist name
- Track details

The user can decide whether to remove some tracks totally or add a new track to the pre-existing tracks. The image below shows how this works when we attempt to update the Album by AC/DC titled "For Those About To Rock We Salute You".

![Update](img/doc-images/update.png)
