import React, { useState } from 'react'
import styled from 'styled-components/macro'
import PropTypes from 'prop-types'

Card.propTypes = {
  title: PropTypes.string,
  question: PropTypes.string.isRequired,
  answer: PropTypes.string.isRequired,
  isBookmarked: PropTypes.bool,
  onBookmarkClick: PropTypes.func,
}

Card.defaultProps = {
  title: '(No title)',
}

export default function Card({ title, question, answer, isBookmarked, onBookmarkClick }) {
  const [isAnswerVisible, setIsAnswerVisible] = useState(false)

  return (
    <CardStyled onClick={toggleAnswer}>
      {onBookmarkClick && <BookmarkStyled onClick={handleBookmarkClick} active={isBookmarked} />}
      <h2>{title}</h2>
      <p>{question}</p>
      {isAnswerVisible && <Answer text={answer} />}
    </CardStyled>
  )

  function Answer({ text }) {
    return (
      <>
        <hr />
        <p>{text}</p>
      </>
    )
  }

  function toggleAnswer() {
    setIsAnswerVisible(!isAnswerVisible)
  }

  function handleBookmarkClick(event) {
    event.stopPropagation()
    onBookmarkClick()
  }
}

const BookmarkStyled = styled.div`
  width: 40px;
  height: 40px;
  position: absolute;
  right: 20px;
  top: -5px;
  background: ${props => (props.active ? 'hotpink' : 'lightgray')};
`

const CardStyled = styled.section`
  position: relative;
  background: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 10px 10px #0002;
`
