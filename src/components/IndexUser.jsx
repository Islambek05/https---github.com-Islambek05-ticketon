import React from "react";

function IndexUser({ events }) {
  const isUpcomingEvent = (date) => new Date(date) > new Date();
  return (
    <div className="container mt-4 d-flex align-content-start flex-wrap">
      {events.length === 0 ? (
        <p>Nothing was found for your query...</p>
      ) : (
        events
          .filter((event) => isUpcomingEvent(event.EventDate))
          .map((event) => (
            <div className="col-sm-5 col-lg-3 mb-4" key={event.EventID}>
              <div className="card event-card">
                <a
                  href={`EventInformation?eventID=${encodeURIComponent(
                    event.EventID
                  )}`}
                >
                  <img
                    src={`data:image/jpeg;base64,${event.EventPoster}`}
                    alt={`${event.EventName} Poster`}
                    className="card-img-top"
                  />
                </a>
                <div className="card-body">
                  <h5 className="card-title">{event.EventName}</h5>
                  <p className="card-text">{event.EventDate}</p>
                </div>
              </div>
            </div>
          ))
      )}
    </div>
  );
}

export default IndexUser;
